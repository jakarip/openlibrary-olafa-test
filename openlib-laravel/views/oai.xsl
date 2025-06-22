<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:oai="http://www.openarchives.org/OAI/2.0/" exclude-result-prefixes="oai">

    <xsl:output method="html" indent="yes"/>


    <!-- TEMPLATE NAVBAR -->
    <xsl:template name="navbar">
        <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #b81414;">

            <div class="container">
                <xsl:variable name="fullURL" select="oai:OAI-PMH/oai:request"/>
                <xsl:variable name="hostURL">
                    <xsl:choose>
                        <xsl:when test="contains(substring-after($fullURL, '://'), '/')">
                            <xsl:value-of select="concat(substring-before($fullURL, '://'), '://', substring-before(substring-after($fullURL, '://'), '/'))"/>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:value-of select="$fullURL"/>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:variable>

                <a class="navbar-brand d-flex align-items-center text-white">
                    <xsl:attribute name="href">
                        <xsl:value-of select="$hostURL"/>
                    </xsl:attribute>
                    <img src="/assets/img/openlibrary/logo-hires.png" alt="Open Library" height="40" class="me-2"/>
                </a>


                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="?verb=Identify">Identify</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="?verb=ListRecords&amp;metadataPrefix=oai_dc">ListRecords</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="?verb=ListSets">ListSets</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="?verb=ListMetadataFormats">ListMetadataFormats</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="?verb=ListIdentifiers&amp;metadataPrefix=oai_dc">ListIdentifiers</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </xsl:template>


    <!-- TEMPLATE UTAMA -->
    <xsl:template match="/">
        <html>
            <head>
                <title>OAI 2.0 Request Results</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            </head>
            <body class="" style="background-color: #f8f7fa;">
                <xsl:call-template name="navbar"/>


                <div class="container my-5">
                    <h1 class="text-center text-dark fw-bold">OAI 2.0 Request Results</h1>
                    <p class="text-center text-muted">
        You are viewing an HTML version of the XML OAI response. To see the underlying XML, use your web browser’s "view source" option.
                    </p>

                    <div class="card p-4 mb-4 shadow border-0">
                        <p class=" text-dark">
                            <strong class="fw-bold">Datestamp of response: </strong>
                            <xsl:value-of select="oai:OAI-PMH/oai:responseDate"/>
                        </p>
                        <p class=" text-dark">
                            <strong class="fw-bold">Request URL: </strong>
                            <xsl:value-of select="oai:OAI-PMH/oai:request"/>
                        </p>
                        <p class=" text-dark">
                            <strong class="fw-bold">Request Type: </strong>
                            <xsl:value-of select="oai:OAI-PMH/oai:request/@verb"/>
                        </p>
                    </div>

                    <xsl:if test="oai:OAI-PMH/oai:error">
                        <div class="alert alert-danger ">
                            <h4 class="alert-heading text-danger">OAI Error(s)</h4>
                            <p>The request could not be completed due to the following error(s):</p>
                            <table class="table table-bordered">
                                <tr class="bg-light">
                                    <th>Error Code</th>
                                    <td>
                                        <xsl:choose>
                                            <xsl:when test="oai:OAI-PMH/oai:error/@code">
                                                <xsl:value-of select="oai:OAI-PMH/oai:error/@code"/>
                                            </xsl:when>
                                            <xsl:otherwise>Unknown Error</xsl:otherwise>
                                        </xsl:choose>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Error Message</th>
                                    <td>
                                        <xsl:choose>
                                            <xsl:when test="oai:OAI-PMH/oai:error">
                                                <xsl:value-of select="oai:OAI-PMH/oai:error"/>
                                            </xsl:when>
                                            <xsl:otherwise>No additional information available</xsl:otherwise>
                                        </xsl:choose>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </xsl:if>

                    <xsl:choose>
                        <xsl:when test="oai:OAI-PMH/oai:request/@verb = 'Identify'">
                            <h2 class="mt-4 text-dark fw-bold">Repository Information</h2>
                            <table class="table table-bordered">
                                <tr class="bg-light">
                                    <th>Repository Name</th>
                                    <td>
                                        <xsl:value-of select="oai:OAI-PMH/oai:Identify/oai:repositoryName"/>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Base URL</th>
                                    <td>
                                        <xsl:value-of select="oai:OAI-PMH/oai:Identify/oai:baseURL"/>
                                    </td>
                                </tr>
                                <tr class="bg-light">
                                    <th>Protocol Version</th>
                                    <td>
                                        <xsl:value-of select="oai:OAI-PMH/oai:Identify/oai:protocolVersion"/>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Earliest Datestamp</th>
                                    <td>
                                        <xsl:value-of select="oai:OAI-PMH/oai:Identify/oai:earliestDatestamp"/>
                                    </td>
                                </tr>
                                <tr class="bg-light">
                                    <th>Deleted Record Policy</th>
                                    <td>
                                        <xsl:value-of select="oai:OAI-PMH/oai:Identify/oai:deletedRecord"/>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Granularity</th>
                                    <td>
                                        <xsl:value-of select="oai:OAI-PMH/oai:Identify/oai:granularity"/>
                                    </td>
                                </tr>
                                <tr class="bg-light">
                                    <th>Admin Email</th>
                                    <td>
                                        <xsl:value-of select="oai:OAI-PMH/oai:Identify/oai:adminEmail"/>
                                    </td>
                                </tr>
                            </table>

                            <h2 class="mt-4 text-dark fw-bold">OAI-Identifier</h2>
                            <table class="table table-bordered">
                                <tr class="bg-light">
                                    <th>Scheme</th>
                                    <td>
                                        <xsl:value-of select="oai:OAI-PMH/oai:Identify/oai:description/*[namespace-uri()='http://www.openarchives.org/OAI/2.0/oai-identifier']/*[local-name()='scheme']"/>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Repository Identifier</th>
                                    <td>
                                        <xsl:value-of select="oai:OAI-PMH/oai:Identify/oai:description/*[namespace-uri()='http://www.openarchives.org/OAI/2.0/oai-identifier']/*[local-name()='repositoryIdentifier']"/>
                                    </td>
                                </tr>
                                <tr class="bg-light">
                                    <th>Delimiter</th>
                                    <td>
                                        <xsl:value-of select="oai:OAI-PMH/oai:Identify/oai:description/*[namespace-uri()='http://www.openarchives.org/OAI/2.0/oai-identifier']/*[local-name()='delimiter']"/>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Sample OAI Identifier</th>
                                    <td>
                                        <xsl:value-of select="oai:OAI-PMH/oai:Identify/oai:description/*[namespace-uri()='http://www.openarchives.org/OAI/2.0/oai-identifier']/*[local-name()='sampleIdentifier']"/>
                                    </td>
                                </tr>
                            </table>
                        </xsl:when>
                    </xsl:choose>


                    <xsl:choose>
                        <xsl:when test="oai:OAI-PMH/oai:request[@verb='ListSets']">
                            <xsl:if test="oai:OAI-PMH/oai:ListSets/oai:set">
                                <style>
                table { width: 100%; table-layout: fixed; }
                th, td { width: 50%; text-align: left; }
                                </style>

                                <xsl:for-each select="oai:OAI-PMH/oai:ListSets/oai:set">
                                    <h2>Set</h2>
                                    <table class="table table-striped table-bordered">
                                        <tr>
                                            <th>SetName</th>
                                            <td>
                                                <xsl:value-of select="oai:setName"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>SetSpec</th>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <xsl:value-of select="oai:setSpec"/>
                                                    <a href="?verb=ListIdentifiers&amp;metadataPrefix=oai_dc&amp;set={oai:setSpec}" class="btn btn-outline-dark btn-sm">Identifiers</a>
                                                    <a href="?verb=ListRecords&amp;metadataPrefix=oai_dc&amp;set={oai:setSpec}" class="btn btn-outline-secondary btn-sm">Records</a>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <br/>
                                </xsl:for-each>
                            </xsl:if>

                            <!-- Jika tidak ada set -->
                            <xsl:if test="not(oai:OAI-PMH/oai:ListSets/oai:set)">
                                <p class="text-muted">No sets available.</p>
                            </xsl:if>
                        </xsl:when>

                    </xsl:choose>


                    <xsl:if test="oai:OAI-PMH/oai:ListIdentifiers">
                        <h2 class="mt-4 text-center text-dark fw-semibold">List of Identifiers</h2>
                        <div class="row">
                            <xsl:for-each select="oai:OAI-PMH/oai:ListIdentifiers/oai:header">
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                                    <div class="card shadow border-0 h-100">
                                        <div class="card-body">
                                            <h6 class="text-muted mb-2">OAI Record</h6>

                                            <!-- Identifier -->
                                            <p class="mb-2">
                                                <strong class="text-dark">Identifier: </strong>
                                                <span class="d-block text-truncate text-primary" style="max-width: 100%;">
                                                    <xsl:value-of select="oai:identifier"/>
                                                </span>
                                            </p>

                                            <!-- Datestamp -->
                                            <p class="mb-1 text-muted small">
                                                <strong>Datestamp: </strong>
                                                <xsl:value-of select="oai:datestamp"/>
                                            </p>

                                            <!-- SetSpec -->
                                            <p class="mb-3">
                                                <strong class="text-dark">SetSpec: </strong>
                                                <span class="badge bg-secondary text-light fw-normal">
                                                    <xsl:value-of select="oai:setSpec"/>
                                                </span>
                                            </p>

                                            <!-- Tombol Actions -->
                                            <div class="d-flex flex-wrap gap-2">
                                                <a href="?verb=GetRecord&amp;metadataPrefix=oai_dc&amp;identifier={oai:identifier}" class="btn btn-dark btn-sm">oai_dc</a>
                                                <a href="?verb=ListMetadataFormats&amp;identifier={oai:identifier}" class="btn btn-outline-dark btn-sm">Format</a>
                                                <a href="?verb=ListIdentifiers&amp;metadataPrefix=oai_dc&amp;set={oai:setSpec}" class="btn btn-dark btn-sm">Identifiers</a>
                                                <a href="?verb=ListRecords&amp;metadataPrefix=oai_dc&amp;set={oai:setSpec}" class="btn btn-outline-dark btn-sm">Records</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </xsl:for-each>
                        </div>

                        <!-- Pagination -->
                        <xsl:if test="oai:OAI-PMH/oai:ListIdentifiers/oai:resumptionToken">
                            <div class="text-center mt-4">
                                <xsl:variable name="setSpecValue">
                                    <xsl:value-of select="oai:OAI-PMH/oai:request/@set"/>
                                </xsl:variable>

                                <xsl:choose>
                                    <xsl:when test="normalize-space($setSpecValue) != ''">
                                        <a class="btn btn-dark" href="?verb=ListIdentifiers&amp;metadataPrefix=oai_dc&amp;set={$setSpecValue}&amp;resumptionToken={oai:OAI-PMH/oai:ListIdentifiers/oai:resumptionToken}">
                        Load More
                                        </a>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <a class="btn btn-dark" href="?verb=ListIdentifiers&amp;metadataPrefix=oai_dc&amp;resumptionToken={oai:OAI-PMH/oai:ListIdentifiers/oai:resumptionToken}">
                        Load More
                                        </a>
                                    </xsl:otherwise>
                                </xsl:choose>
                            </div>
                        </xsl:if>
                    </xsl:if>

                    <xsl:if test="oai:OAI-PMH/oai:GetRecord">
                        <h2 class="mt-4 text-center">OAI Record</h2>
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Card utama -->
                                    <div class="card mb-4 border-dark">
                                        <div class="card-body">
                                            <h5 class="card-title">OAI Identifier</h5>
                                            <p>
                                                <strong>ID: </strong>
                                                <xsl:value-of select="oai:OAI-PMH/oai:GetRecord/oai:record/oai:header/oai:identifier"/>
                                                <div class="d-flex gap-2">
                                                    <a href="?verb=GetRecord&amp;metadataPrefix=oai_dc&amp;identifier={oai:OAI-PMH/oai:GetRecord/oai:record/oai:header/oai:identifier}" class="btn btn-dark btn-sm">oai_dc</a>
                                                    <a href="?verb=ListMetadataFormats&amp;identifier={oai:OAI-PMH/oai:GetRecord/oai:record/oai:header/oai:identifier}" class="btn btn-dark btn-sm">Formats</a>
                                                </div>
                                            </p>

                                            <p>
                                                <strong>Datestamp: </strong>
                                                <xsl:value-of select="oai:OAI-PMH/oai:GetRecord/oai:record/oai:header/oai:datestamp"/>
                                            </p>
                                            <p>
                                                <strong>setSpec: </strong>
                                                <xsl:value-of select="oai:OAI-PMH/oai:GetRecord/oai:record/oai:header/oai:setSpec"/>
                                                <div class="d-flex gap-2">
                                                    <a href="?verb=ListIdentifiers&amp;metadataPrefix=oai_dc&amp;set={oai:OAI-PMH/oai:GetRecord/oai:record/oai:header/oai:setSpec}" class="btn btn-primary btn-sm">Identifiers</a>
                                                    <a href="?verb=ListRecords&amp;metadataPrefix=oai_dc&amp;set={oai:OAI-PMH/oai:GetRecord/oai:record/oai:header/oai:setSpec}" class="btn btn-secondary btn-sm">Records</a>
                                                </div>
                                            </p>

                                            <!-- Card Metadata -->
                                            <div class="card bg-light mt-3">
                                                <div class="card-body">
                                                    <h5 class="card-title">Metadata</h5>
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <th>Title</th>
                                                            <td>
                                                                <xsl:value-of select="oai:OAI-PMH/oai:GetRecord/oai:record/oai:metadata/*[namespace-uri()='http://www.openarchives.org/OAI/2.0/oai_dc/']/*[local-name()='title']"/>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Author or Creator</th>
                                                            <td>
                                                                <xsl:value-of select="oai:OAI-PMH/oai:GetRecord/oai:record/oai:metadata/*[namespace-uri()='http://www.openarchives.org/OAI/2.0/oai_dc/']/*[local-name()='creator']"/>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Subject and Keywords</th>
                                                            <td>
                                                                <xsl:value-of select="oai:OAI-PMH/oai:GetRecord/oai:record/oai:metadata/*[namespace-uri()='http://www.openarchives.org/OAI/2.0/oai_dc/']/*[local-name()='subject']"/>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Publisher</th>
                                                            <td>
                                                                <xsl:value-of select="oai:OAI-PMH/oai:GetRecord/oai:record/oai:metadata/*[namespace-uri()='http://www.openarchives.org/OAI/2.0/oai_dc/']/*[local-name()='publisher']"/>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Date</th>
                                                            <td>
                                                                <xsl:value-of select="oai:OAI-PMH/oai:GetRecord/oai:record/oai:metadata/*[namespace-uri()='http://www.openarchives.org/OAI/2.0/oai_dc/']/*[local-name()='date']"/>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Resource Type</th>
                                                            <td>
                                                                <xsl:value-of select="oai:OAI-PMH/oai:GetRecord/oai:record/oai:metadata/*[namespace-uri()='http://www.openarchives.org/OAI/2.0/oai_dc/']/*[local-name()='type']"/>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Resource Identifier</th>
                                                            <td>
                                                                <a href="{oai:OAI-PMH/oai:GetRecord/oai:record/oai:metadata/*[namespace-uri()='http://www.openarchives.org/OAI/2.0/oai_dc/']/*[local-name()='identifier']}" target="_blank">
                                                                    <xsl:value-of select="oai:OAI-PMH/oai:GetRecord/oai:record/oai:metadata/*[namespace-uri()='http://www.openarchives.org/OAI/2.0/oai_dc/']/*[local-name()='identifier']"/>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Language</th>
                                                            <td>
                                                                <xsl:value-of select="oai:OAI-PMH/oai:GetRecord/oai:record/oai:metadata/*[namespace-uri()='http://www.openarchives.org/OAI/2.0/oai_dc/']/*[local-name()='language']"/>
                                                            </td>
                                                        </tr>

                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </xsl:if>




                    <xsl:if test="oai:OAI-PMH/oai:ListMetadataFormats">
                        <h2 class="mt-4">Metadata Format</h2>
                        <p>This is a list of metadata formats available from this archive.</p>
                        <table class="table table-bordered">
                            <xsl:for-each select="oai:OAI-PMH/oai:ListMetadataFormats/oai:metadataFormat">
                                <tr>
                                    <th style="background-color: #ccccff; padding: 8px;">metadataPrefix</th>
                                    <td style="padding: 8px;">
                                        <xsl:value-of select="oai:metadataPrefix"/>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="background-color: #ccccff; padding: 8px;">metadataNamespace</th>
                                    <td style="padding: 8px;">
                                        <xsl:value-of select="oai:metadataNamespace"/>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="background-color: #ccccff; padding: 8px;">schema</th>
                                    <td style="padding: 8px;">
                                        <a href="{oai:schema}" target="_blank">
                                            <xsl:value-of select="oai:schema"/>
                                        </a>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </xsl:if>

                    <xsl:if test="oai:OAI-PMH/oai:ListRecords">
                        <h2 class="mt-4 text-center">List of Records</h2>
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <xsl:for-each select="oai:OAI-PMH/oai:ListRecords/oai:record">
                                        <!-- Card utama -->
                                        <div class="card mb-4 border-dark">
                                            <div class="card-body">
                                                <h5 class="card-title">OAI Identifier</h5>
                                                <p>
                                                    <strong>ID: </strong>
                                                    <xsl:value-of select="oai:header/oai:identifier"/>
                                                    <div class="d-flex gap-2">
                                                        <a href="?verb=GetRecord&amp;metadataPrefix=oai_dc&amp;identifier={oai:header/oai:identifier}" class="btn btn-dark btn-sm">oai_dc</a>
                                                        <a href="?verb=ListMetadataFormats&amp;identifier={oai:header/oai:identifier}" class="btn btn-dark btn-sm">Formats</a>
                                                    </div>
                                                </p>

                                                <p>
                                                    <strong>Datestamp: </strong>
                                                    <xsl:value-of select="oai:header/oai:datestamp"/>
                                                </p>
                                                <p>
                                                    <strong>SetSpec: </strong>
                                                    <xsl:value-of select="oai:header/oai:setSpec"/>
                                                    <div class="d-flex gap-2">
                                                        <a href="?verb=ListIdentifiers&amp;metadataPrefix=oai_dc&amp;set={oai:header/oai:setSpec}" class="btn btn-primary btn-sm">Identifiers</a>
                                                        <a href="?verb=ListRecords&amp;metadataPrefix=oai_dc&amp;set={oai:header/oai:setSpec}" class="btn btn-secondary btn-sm">Records</a>
                                                    </div>
                                                </p>

                                                <!-- Card Metadata -->
                                                <div class="card bg-light mt-3">
                                                    <div class="card-body">
                                                        <h5 class="card-title">Metadata</h5>
                                                        <table class="table table-bordered">
                                                            <tr>
                                                                <th>Title</th>
                                                                <td>
                                                                    <xsl:value-of select="oai:metadata/*[namespace-uri()='http://www.openarchives.org/OAI/2.0/oai_dc/']/*[local-name()='title']"/>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Author or Creator</th>
                                                                <td>
                                                                    <xsl:value-of select="oai:metadata/*[namespace-uri()='http://www.openarchives.org/OAI/2.0/oai_dc/']/*[local-name()='creator']"/>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Subject and Keywords</th>
                                                                <td>
                                                                    <xsl:value-of select="oai:metadata/*[namespace-uri()='http://www.openarchives.org/OAI/2.0/oai_dc/']/*[local-name()='subject']"/>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Publisher</th>
                                                                <td>
                                                                    <xsl:value-of select="oai:metadata/*[namespace-uri()='http://www.openarchives.org/OAI/2.0/oai_dc/']/*[local-name()='publisher']"/>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Date</th>
                                                                <td>
                                                                    <xsl:value-of select="oai:metadata/*[namespace-uri()='http://www.openarchives.org/OAI/2.0/oai_dc/']/*[local-name()='date']"/>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Resource Type</th>
                                                                <td>
                                                                    <xsl:value-of select="oai:metadata/*[namespace-uri()='http://www.openarchives.org/OAI/2.0/oai_dc/']/*[local-name()='type']"/>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Resource Identifier</th>
                                                                <td>
                                                                    <xsl:for-each select="oai:metadata/*[namespace-uri()='http://www.openarchives.org/OAI/2.0/oai_dc/']/*[local-name()='identifier']">
                                                                        <a href="{normalize-space(.)}" target="_blank">
                                                                            <xsl:value-of select="normalize-space(.)"/>
                                                                        </a>
                                                                        <br/>
                                                                    </xsl:for-each>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Language</th>
                                                                <td>
                                                                    <xsl:value-of select="oai:metadata/*[namespace-uri()='http://www.openarchives.org/OAI/2.0/oai_dc/']/*[local-name()='language']"/>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </xsl:for-each>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <xsl:if test="oai:OAI-PMH/oai:ListRecords/oai:resumptionToken">
                            <div class="text-center mt-4">
                                <xsl:variable name="setSpecValue">
                                    <xsl:value-of select="oai:OAI-PMH/oai:request/@set"/>
                                </xsl:variable>

                                <xsl:choose>
                                    <xsl:when test="normalize-space($setSpecValue) != ''">
                                        <a class="btn btn-outline-primary" href="?verb=ListRecords&amp;metadataPrefix=oai_dc&amp;set={$setSpecValue}&amp;resumptionToken={oai:OAI-PMH/oai:ListRecords/oai:resumptionToken}">
                        Load More
                                        </a>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <a class="btn btn-outline-primary" href="?verb=ListRecords&amp;metadataPrefix=oai_dc&amp;resumptionToken={oai:OAI-PMH/oai:ListRecords/oai:resumptionToken}">
                        Load More
                                        </a>
                                    </xsl:otherwise>
                                </xsl:choose>
                            </div>
                        </xsl:if>
                    </xsl:if>


                    <!-- Debugging XML Data -->
                    <div class="card p-3 bg-light mt-4">
                        <h3>Debugging XML Data:</h3>
                        <xsl:copy-of select="oai:OAI-PMH"/>
                    </div>

                    <!-- Footer -->
                    <footer class="text-center py-3 mt-4" style="background-color: #ffffff; border-top: 3px solid #b81414;">
                        <p class="text-muted">More information about this XSLT is at the <a href="http://www.openarchives.org/OAI/2.0/">OAI-PMH homepage</a>.
                           
                        </p>
                    </footer>
                </div>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
