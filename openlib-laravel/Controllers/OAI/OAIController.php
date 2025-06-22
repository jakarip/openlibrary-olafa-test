<?php

namespace App\Http\Controllers\OAI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use App\Models\OAI\Record;

class OAIController extends Controller
{
    public function handleRequest(Request $request)
    {
        $validVerbs = ['Identify', 'ListMetadataFormats', 'ListIdentifiers', 'ListRecords', 'GetRecord', 'ListSets'];
        $validMetadataFormats = ['oai_dc'];
        $allowedParams = ['verb', 'metadataPrefix', 'identifier', 'set', 'resumptionToken'];

        $verb = $request->query('verb');
        $metadataPrefix = $request->query('metadataPrefix');

        foreach (array_keys($request->query()) as $param) {
            if (!in_array($param, $allowedParams)) {
                return $this->badArgumentError("Unknown parameter: '$param'");
            }
        }

        if (!$verb || !in_array($verb, $validVerbs)) {
            return $this->badVerbError();
        }

        if ($metadataPrefix && !in_array($metadataPrefix, $validMetadataFormats)) {
            return $this->badMetadataFormatError();
        }

        switch ($verb) {
            case 'Identify':
                return $this->identify();
            case 'ListMetadataFormats':
                return $this->listMetadataFormats();
            case 'ListIdentifiers':
                return $this->listIdentifiers();
            case 'ListRecords':
                return $this->listRecords();
            case 'GetRecord':
                return $this->getRecord($request->query('identifier'));
            case 'ListSets':
                return $this->listSets();
            default:
                return $this->badVerbError();
        }
    }




    /**
     * Serve XSLT file from resources/views/oai.xsl
     */
    public function serveXSLT()
    {
        $path = resource_path('views/oai.xsl');

        if (!File::exists($path)) {
            abort(404, "XSLT file not found.");
        }

        return response(File::get($path), 200)->header('Content-Type', 'text/xml');
    }

    /**
     * OAI-PMH Identify
     */
    private function identify()
    {
        $repositoryIdentifier = "openlibrary.telkomuniversity.ac.id";

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="' . route('oai.xsl') . '"?>' . "\n";
        $xml .= '<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/">
        <responseDate>' . now()->toIso8601String() . '</responseDate>
        <request verb="Identify">' . url('/oai') . '</request>
        <Identify>
            <repositoryName>Open Library</repositoryName>
            <baseURL>' . url('/oai') . '</baseURL>
            <protocolVersion>2.0</protocolVersion>
            <adminEmail>telkomopenlibrary@gmail.com</adminEmail>
            <earliestDatestamp>1981-07-01</earliestDatestamp>
            <deletedRecord>transient</deletedRecord>
            <granularity>YYYY-MM-DD</granularity>

            <description>
                <oai-identifier xmlns="http://www.openarchives.org/OAI/2.0/oai-identifier"
                                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                                xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai-identifier
                                                    http://www.openarchives.org/OAI/2.0/oai-identifier.xsd">
                    <scheme>oai</scheme>
                    <repositoryIdentifier>' . $repositoryIdentifier . '</repositoryIdentifier>
                    <delimiter>:</delimiter>
                    <sampleIdentifier>oai:' . $repositoryIdentifier . ':14.02.120</sampleIdentifier>
                </oai-identifier>
            </description>
        </Identify>
    </OAI-PMH>';

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }


    /**
     * OAI-PMH ListMetadataFormats
     */
    private function listMetadataFormats()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="' . route('oai.xsl') . '"?>' . "\n";
        $xml .= '<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/">
        <responseDate>' . now()->toIso8601String() . '</responseDate>
        <request verb="ListMetadataFormats">' . url('/oai') . '</request>
        <ListMetadataFormats>
            <metadataFormat>
                <metadataPrefix>oai_dc</metadataPrefix>
                <schema>http://www.openarchives.org/OAI/2.0/oai_dc.xsd</schema>
                <metadataNamespace>http://www.openarchives.org/OAI/2.0/</metadataNamespace>
            </metadataFormat>
        </ListMetadataFormats>
    </OAI-PMH>';

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }

    /**
     * OAI-PMH ListIdentifiers
     */
    private function listIdentifiers()
    {
        $perPage = 50;
        $resumptionToken = request('resumptionToken');
        $setId = request('set');

        if ($resumptionToken) {
            $page = (int) $resumptionToken;
        } else {
            $page = 1;
        }

        $query = Record::orderBy('id');
        if ($setId) {
            $query->whereHas('type', function ($q) use ($setId) {
                $q->where('id', $setId);
            });
        }

        $records = $query->paginate($perPage, ['*'], 'page', $page);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="' . route('oai.xsl') . '"?>' . "\n";
        $xml .= '<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/">
        <responseDate>' . now()->toIso8601String() . '</responseDate>
        <request verb="ListIdentifiers"';
        if ($setId) {
            $xml .= ' set="' . htmlspecialchars($setId) . '"';
        }
        $xml .= '>' . url('/oai') . '</request>
        <ListIdentifiers>';

        foreach ($records as $record) {
            $setSpecParts = explode(':', $record->setSpec);
            $recordSetId = $setSpecParts[0] ?? '';
            $recordSetName = $setSpecParts[1] ?? ''; // Nama untuk tampila

            $xml .= '
    <header>
        <identifier>oai:openlibrary.telkomuniversity.ac.id:' . $record->kode_katalog . '</identifier>
        <datestamp>' . \Carbon\Carbon::parse($record->created_at)->format('Y-m-d\TH:i:s\Z') . '</datestamp>
        <setSpec>' . htmlspecialchars($recordSetId) . '</setSpec>
    </header>';
        }

        if ($records->hasMorePages()) {
            $nextPage = $page + 1;
            $xml .= '<resumptionToken>' . htmlspecialchars($nextPage) . '</resumptionToken>';
        }

        $xml .= '
    </ListIdentifiers>
</OAI-PMH>';

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }

    private function listSets()
    {
        $sets = Record::select('knowledge_type_id')
            ->distinct()
            ->with('type')
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="' . route('oai.xsl') . '"?>' . "\n";
        $xml .= '<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/">
    <responseDate>' . now()->toIso8601String() . '</responseDate>
    <request verb="ListSets">' . url('/oai') . '</request>
    <ListSets>';

        foreach ($sets as $set) {
            if ($set->type) {
                $xml .= '
            <set>
                <setSpec>' . $set->type->id . '</setSpec> <!-- Gunakan ID untuk filter -->
                <setName>' . htmlspecialchars($set->type->name) . '</setName> <!-- Tampilkan Nama -->
            </set>';
            }
        }

        $xml .= '
    </ListSets>
</OAI-PMH>';

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }


    /**
     * Error Handler - badVerb
     */


    /**
     * Error: Verb tidak valid
     */
    private function badVerbError()
    {
        return $this->generateOaiError("badVerb", "The verb argument is not valid, missing, or repeated.");
    }

    /**
     * Error: metadataPrefix tidak valid
     */
    private function badMetadataFormatError()
    {
        return $this->generateOaiError("cannotDisseminateFormat", "The metadata format given is not supported by this repository.");
    }

    /**
     * Error: Parameter tidak dikenal
     */
    private function badArgumentError($message)
    {
        return $this->generateOaiError("badArgument", $message);
    }

    /**
     * Fungsi utama untuk membentuk error XML
     */
    private function generateOaiError($code, $message)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="' . route('oai.xsl') . '"?>' . "\n";
        $xml .= '<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/">
        <responseDate>' . now()->toIso8601String() . '</responseDate>
        <request>' . url('/oai') . '</request>
        <error code="' . htmlspecialchars($code) . '">' . htmlspecialchars($message) . '</error>
    </OAI-PMH>';

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }


    private function getRecord($identifier)
    {
        $identifier = str_replace('oai:openlibrary.telkomuniversity.ac.id:', '', $identifier);

        $record = Record::whereHas('classification', function ($query) use ($identifier) {
            $query->where('code', $identifier);
        })->with(['type', 'subject', 'classification'])->first();

        if (!$record) {
            return $this->badVerbError();
        }

        $setSpecParts = explode(':', $record->setSpec ?? 'default');
        $recordSetId = $setSpecParts[0] ?? 'default';

        $originalType = $record->type->name ?? 'Unknown';
        $mappedType = in_array($originalType, [
            "Karya Ilmiah - Skripsi (S1) - Reference",
            "Karya Ilmiah - Thesis (S2) - Reference",
            "Karya Ilmiah - TA (D3) - Reference",
            "Karya Ilmiah - Disertasi (S3) - Reference"
        ]) ? "Thesis" : $originalType;

        $formattedTitle = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $record->title), '-'));
        $resourceIdentifier = "https://openlibrary.telkomuniversity.ac.id/pustaka/{$record->id}/{$formattedTitle}.html";

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="' . route('oai.xsl') . '"?>' . "\n";
        $xml .= '<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/">
<responseDate>' . now()->toIso8601String() . '</responseDate>
<request verb="GetRecord" metadataPrefix="oai_dc" identifier="' . htmlspecialchars($identifier) . '">' . url('/oai') . '</request>
<GetRecord>
    <record>
        <header>
            <identifier>oai:openlibrary.telkomuniversity.ac.id:' . htmlspecialchars($identifier) . '</identifier>
            <datestamp>' . \Carbon\Carbon::parse($record->created_at)->format('Y-m-d\TH:i:s\Z') . '</datestamp>
            <setSpec>' . htmlspecialchars($recordSetId) . '</setSpec>
        </header>
        <metadata>
            <oai_dc:dc xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/"
                       xmlns:dc="http://purl.org/dc/elements/1.1/"
                       xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                       xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/
                                           http://www.openarchives.org/OAI/2.0/oai_dc.xsd">
                <dc:title>' . htmlspecialchars($record->title) . '</dc:title>
                <dc:creator>' . htmlspecialchars($record->author) . '</dc:creator>
                <dc:subject>' . htmlspecialchars($record->subject->name ?? 'Unknown') . '</dc:subject>
                <dc:publisher>' . htmlspecialchars($record->publisher_name ?? 'Unknown') . '</dc:publisher>
                <dc:date>' . htmlspecialchars($record->published_year ?? 'Unknown') . '</dc:date>
                <dc:type>' . htmlspecialchars($mappedType) . '</dc:type>
                <dc:identifier>' . htmlspecialchars($resourceIdentifier) . '</dc:identifier>
                <dc:language>' . htmlspecialchars($record->language ?? 'Unknown') . '</dc:language>
            </oai_dc:dc>
        </metadata>
    </record>
</GetRecord>
</OAI-PMH>';

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }


    private function listRecords()
    {
        $perPage = 50;
        $resumptionToken = request('resumptionToken');
        $setId = request('set');

        if ($resumptionToken) {
            $page = (int) $resumptionToken;
        } else {
            $page = 1;
        }

        $query = Record::orderBy('id')->with(['type', 'subject', 'classification']);
        if ($setId) {
            $query->whereHas('type', function ($q) use ($setId) {
                $q->where('id', $setId);
            });
        }

        $records = $query->paginate($perPage, ['*'], 'page', $page);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="' . route('oai.xsl') . '"?>' . "\n";
        $xml .= '<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/">
<responseDate>' . now()->toIso8601String() . '</responseDate>
<request verb="ListRecords"';
        if ($setId) {
            $xml .= ' set="' . htmlspecialchars($setId) . '"';
        }
        $xml .= '>' . url('/oai') . '</request>
<ListRecords>';

        foreach ($records as $record) {
            $setSpecParts = explode(':', $record->setSpec);
            $recordSetId = $setSpecParts[0] ?? '';
            $recordSetName = $setSpecParts[1] ?? '';
            $catalogCode = $record->classification->code ?? 'Unknown';

            $originalType = $record->type->name ?? 'Unknown';
            $mappedType = in_array($originalType, [
                "Karya Ilmiah - Skripsi (S1) - Reference",
                "Karya Ilmiah - Thesis (S2) - Reference",
                "Karya Ilmiah - TA (D3) - Reference",
                "Karya Ilmiah - Disertasi (S3) - Reference"
            ]) ? "Thesis" : $originalType;

            $formattedTitle = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $record->title), '-'));
            $resourceIdentifier = "https://openlibrary.telkomuniversity.ac.id/pustaka/{$record->id}/{$formattedTitle}.html";

            $xml .= '
<record>
    <header>
        <identifier>oai:openlibrary.telkomuniversity.ac.id:' . $catalogCode . '</identifier>
        <datestamp>' . \Carbon\Carbon::parse($record->created_at)->format('Y-m-d\TH:i:s\Z') . '</datestamp>
        <setSpec>' . htmlspecialchars($recordSetId) . '</setSpec>
    </header>
    <metadata>
        <oai_dc:dc xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/"
                    xmlns:dc="http://purl.org/dc/elements/1.1/"
                    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                    xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/
                                        http://www.openarchives.org/OAI/2.0/oai_dc.xsd">
            <dc:title>' . htmlspecialchars($record->title) . '</dc:title>
            <dc:creator>' . htmlspecialchars($record->author) . '</dc:creator>
            <dc:subject>' . htmlspecialchars($record->subject->name ?? 'Unknown') . '</dc:subject>
            <dc:publisher>' . htmlspecialchars($record->publisher_name ?? 'Unknown') . '</dc:publisher>
            <dc:date>' . htmlspecialchars($record->published_year ?? 'Unknown') . '</dc:date>
            <dc:type>' . htmlspecialchars($mappedType) . '</dc:type>
            <dc:identifier>' . htmlspecialchars($resourceIdentifier) . '</dc:identifier>
            <dc:language>' . htmlspecialchars($record->language ?? 'Unknown') . '</dc:language>
        </oai_dc:dc>
    </metadata>
</record>';
        }

        if ($records->hasMorePages()) {
            $nextPage = $page + 1;
            $xml .= '<resumptionToken>' . htmlspecialchars($nextPage) . '</resumptionToken>';
        }

        $xml .= '
</ListRecords>
</OAI-PMH>';

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }


}

