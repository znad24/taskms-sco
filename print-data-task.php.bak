
<?php

include("authentication.php");

?>

		
		  
	  <table class="table-striped" id="myTable" style="width:100%;" border="0"> 

  <tr>
    <td colspan="2"><b>Daily Task Report</b></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2">Departement</td>
    <td>:&nbsp; &nbsp; ICT</td>
  </tr>
  <tr>
    <td colspan="2">Area / Lokasi</td>
    <td>:&nbsp; &nbsp; SCO</td>
  </tr>
  <tr>
    <td colspan="2">Export Date</td>
    <td>:&nbsp; &nbsp; <?php echo date('M d, Y');?></td>
	
  </tr>
<tr>
<td colspan="2">&nbsp;</td>
</tr>


    <thead>		  

              <thead>
			   <table border="1">
                <tr>
                  <th>NO</th>
                  <th>Task Title</th>
				  <th>Task Category</th>
                  <th>Technical Support</th>
                  <th>Start Time</th>
                  <th>End Time</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>


              <?php 
			  
                include("inc/koneksi.php");
			 
				$query_mysql = mysql_query("SELECT a.*, b.fullname 
                        FROM task_info a
                        INNER JOIN tbl_admin b
						ON(a.t_user_id = b.user_id)")or die(mysql_error());
				$i=1;
				while($data = mysql_fetch_array($query_mysql)){
				
              ?>			  
			  
                <tr>
                  <td><?php echo $i++; ?></td>
                  <td><?php echo $data['t_title']; ?></td>
				  <td><?php echo $data['t_category']; ?></td>
                  <td><?php echo $data['fullname']; ?></td>
                  <td><?php echo $data['t_start_time']; ?></td>
                  <td><?php echo $data['t_end_time']; ?></td>
                  <td>
					<?php  if($data['status'] == 0){
							echo 'In Complete';
					}elseif ($data['status'] == 1){
							echo 'In Progress';		
					 }elseif($data['status'] == 2){
                        echo 'Completed';
                    
                    } ?>
                    
                  </td>
                </tr>
                <?php } ?>
                
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>


<?php

include("include/footer.php");



?>
<noscript>
    <div>
        <style>
            body{
                background-image:none !important;
            }
            .mb-0{
                margin:0px;
            }
        </style>
        <div style="line-height:1em">
        <h4 class="mb-0 text-center"><b>Employee Task Managament System</b></h4>
        <h4 class="mb-0 text-center"><b>Daily Task Report</b></h4>
        <div class="mb-0 text-center"><b>as of</b></div>
        <div class="mb-0 text-center"><b><?= date("F d, Y", strtotime($date)) ?></b></div>
        </div>
        <hr>
    </div>
</noscript>

<script type="text/javascript">
$(function(){
    $('#filter').click(function(){
        location.href="./daily-task-report.php?date="+$('#date').val()
    })
    $('#print').click(function(){
        var h = $('head').clone()
        var ns = $($('noscript').html()).clone()
        var p = $('#printout').clone()
        var base = '<?= $base_url ?>';
        h.find('link').each(function(){
            $(this).attr('href', base + $(this).attr('href'))
        })
        h.find('script').each(function(){
            if($(this).attr('src') != "")
            $(this).attr('src', base + $(this).attr('src'))
        })
        p.find('.table').addClass('table-bordered')
        var nw = window.open("", "_blank","width:"+($(window).width() * .8)+",left:"+($(window).width() * .1)+",height:"+($(window).height() * .8)+",top:"+($(window).height() * .1))
            nw.document.querySelector('head').innerHTML = h.html()
            nw.document.querySelector('body').innerHTML = ns[0].outerHTML
            nw.document.querySelector('body').innerHTML += p[0].outerHTML
            nw.document.close()
            setTimeout(() => {
                nw.print()
                setTimeout(() => {
                    nw.close()
                }, 200);
            }, 200);

    })
})
</script>
