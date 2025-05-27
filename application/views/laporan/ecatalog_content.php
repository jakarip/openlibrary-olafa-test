	
	
	<?php 
							if(ISSET($class)){
							foreach ($class as $i => $val){
							
								if (count($data[$i])!=0) {
							?>
									<div width="100%" style="font-size:14px;font-weight:bold;font-family:calibri"><?php echo $i."00 - ".$i."99 ".$val;?></div>
									<hr style="border-top:1px;">
                                    <table class="table table-striped responsive-utilities jambo_table bulk_action">  
								<tbody>
							<?php 
							
							
							$no		= 1; 
							$style 	= 'even pointer'; 
							 
							foreach ($data[$i] as $row) {    
								if ($row->cover_path!="" and file_exists('../../../../data/batik/www/uploads/book/cover/'.$row->cover_path)) $file = $row->cover_path; 
								else $file="default.jpg";
								
								$title = clean(strtolower($row->title)); 
								$url   = "https://openlibrary.telkomuniversity.ac.id/pustaka/".$row->cat_id."/".$title.".html";
							?>
							<tr class="<?php echo $style?>"> 
								<td  width="15%" align="center"><img src="/uploads/book/cover/<?php echo $file ?>" width="75"><br><?php echo $row->cat_code ?></td>
								<td width="50%"><?php 
									echo "<a href='".$url."'>".$row->title."</a><br>";
									echo $row->author."<br>";
									echo $row->publisher_name.", ".$row->published_year."<br>";
									echo "Klasifikasi ".$row->class_name."<br>";
									echo $row->tipe." (Sirkulasi)<br>";
									echo "<a href='https://openlibrary.telkomuniversity.ac.id/knowledgeitem/".$row->cat_id."/available.html'>Total ".$row->eks." Koleksi</a><br>"; 
									echo $url;
								?></td>
								<td width="35%" ><?php 
									echo "<b>".$row->subject."</b><br>";
									echo ($row->alternate_subject!=""?$row->alternate_subject."<br>":"");
								echo limit_text($row->abstract_content,30)."<a href='".$url."'>selengkapnya..</a><br>";
								?></td>
								
							</tr>


							<?php 
								$no++; 
								if($style 	= 'even pointer') $style 	= 'odd pointer'; 
								else $style 	= 'even pointer'; 
							?>
							 
							<?php 	} ?>

                               
                                     </tbody>

                                    </table>
							<?php 	}
									} 
									} ?>