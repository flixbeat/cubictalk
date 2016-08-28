<?php Configure::write('debug',0)?>

<style>
	td{
	  vertical-align: top;
	  text-align: left;
	}
	.column{
		border: 1px solid black;
		padding: 5px;
		margin: 5px;
		float: left;	
		width: 500px;
	}
	#wrapper{
		width: 500px;
		margin-left: 5%;
		margin: auto;
	}
</style>
<?php echo $javascript->link('jquery-1.4.2.min');?>

<div id = "wrapper">
	<h2>Language Routes</h2>

	<div id = "col1" class = "column">
		<h4>Insert</h4>
		<hr>
		<table>
			<tr>
				<td><button id = "btnNewCont">Add New Controller</button></td>
				<td><button id = "btnNewAct">Add New Action to Controller</button></td>
			</tr>
		</table>
		<?php echo $html->link('Go to language management >',array('controller'=>'language','action'=>'index'));?><br>
		<?php echo $html->link('Go to teacher_mypage >',array('controller'=>'lessons','action'=>'teacher_management'));?>

		<div id = "add_controller">
			<table>
				<tr>
					<th colspan = "3">Add new controller:<hr/></th>
				</tr>
				<tr>
					<td>Controller Name: </td>
					<td><input type = "text" id = "tbNewController"></td>
					<td><button id = "btnAddController">Add</button></td>
				</tr>
			</table>
		</div>



		<div id = "add_action">
			<table>
				<tr>
					<th colspan = "3">Add action to controller:<hr/></th>
				</tr>
				<tr>
					<td>Select Controller</td>
					<td colspan = "2">
						<select id = "cbController">
							<?php 
								foreach ($controllers as $controller) {
									echo '<option>'.$controller['language_routes']['controller'].'</option>';
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>New Action</td>
					<td>
						<input type = "text" id = "tbNewAction" name = "data[LanguageRoute][action]">
						<input type = "hidden" id = "tbController" name = "data[LanguageRoute][controller]">
					</td>
					<td>
					<td>
						<button id = "addAction">Add</button>
					</td>
				</tr>
			</table>
			
		</div>

		<br>
		<table>
			<tr>
				<td>
					<table id = "controller_table" border = "1">
						
					</table>
				</td>
				<td>
					<table id = "action_table" border = "1">
						
					</table>
				</td>
			</tr>
		</table>
	</div>

	<!-- ARNIE's ==================================================================================== -->

	<div id = "col2" class = "column">
		<h4>Delete</h4>
		<hr>
		<table>
			<tr>
				<td>
					Controller
						<select id = 'sel'>
							<?php 
							//controller delete
								for($i=0; $i<count($options); $i+=1) {
									$controller = $options[$i]['language_routes']['controller'];
									echo "<option>$controller</option>";
								}
							?>
						</select>
						<input type = "hidden" name = "data[LanguageRoute][controller]" id = 'tbId' value = ''>
						<input type = "hidden" name = "data[LanguageRoute][controller]" id = 'tbCon' value = ''>
				</td>
				<td>
					<button id = "btnDelAct">Delete Actions >> </button>
				</td>
			</tr>	
		</table>
	</div>


	<!-- KARL's ==================================================================================== -->

	<div id = "col3" class = "column">
		<h4>Update</h4>
		<hr>
		<?php echo $javascript->link('jquery-1.4.2.min');?>

		<?php
			echo $form->create(null,array('action'=>'update_controller'));
		?>
		<table>
			<tr>
				<td>Controller</td>
				<td>
					<select id="selx">
						<?php
							for($i=0;$i<count($data);$i+=1){ 
								
								$controller = $data[$i]['language_routes']['controller'];
								echo "<option>$controller</option>";
							}
						?>	
					</select>
					<input type='hidden' name = "data[LanguageRoute][old_controller]" id='myhid' value='' >
					
				</td>
			</tr>
			<tr>
				<td>New Controller </td>
				<td>
					<input name="data[LanguageRoute][controller]" value="">
				</td>
			</tr>
		</table>

		<?php
			echo $form->end('Edit');	
		?>
		<br>
		<form id="LanguageRouteIndexForm" method="post" action="/cubictalkta/language_route/next_page_update/">
			<button>Update Actions >> </button>
		</form>
	</div>

</div>
<script>
	$(document).ready(function(){
		$('#add_controller').hide();
		$('#add_action').hide();

		$('#btnNewCont').click(function(){
			$('#add_action').hide();
			$('#add_controller').fadeIn();
		});

		$('#btnNewAct').click(function(){
			$('#add_controller').hide();
			$('#add_action').fadeIn();
		});


		$('#tbController').val($('#cbController').val());

		$('#cbController').change(function(){
			$('#tbController').val($(this).val());
		});

		// ajax

		$('#btnAddController').click(function(){

			var route = 'language_route/ajax';

			// pre ajax access test
			$.post(route,function(data){

				// route fixing
				if( data.indexOf('<') > -1 ){
					route = 'ajax';
					console.log(route);
				}

				// inserting and re-loading of options
				$.post(route,
					{
						action: 'add_controller',
						controller: $('#tbNewController').val(),
						function_action: 'index'
					},
					function(data){
						// console.log(data);
						var json = JSON.parse(data);
						// console.log(json[0]['language_routes']['id']);
						// console.log(json.length);

						$('#cbController').empty();
						for(var i=0;i<json.length;i+=1){
							$('#cbController').append("<option>"+json[i]['language_routes']['controller']+"</option>");
						}
						
						$('#add_controller').hide();
						$('#add_action').fadeIn();

						$('#tbController').val($('#cbController').val());

						// re-loading of controller tables
						$.post(route,{action:'load_controller_table'},function(data){
							$('#controller_table').empty();
							$('#controller_table').append(data);
						});



					}
				);


				// loading of action tables
				
				var c = $('#cbController').val();
				var a = $('#tbNewAction').val();

				$.post(route,
					{
						action:'add_action',
						controller:c,
						act:a,
						loadOnly:true,
					},
					function(data){
						$('#action_table').empty();
						$('#action_table').append(data);
						$('#action_table').fadeIn();
					}
				);
				
			});
		});

		$('#btnNewCont').click(function(){

			var route = 'language_route/ajax';

			// pre ajax access test
			$.post(route,function(data){
				// route fixing
				if( data.indexOf('<') > -1 ){
					route = 'ajax';
					console.log(route);
				}

				// loading of controller tables
				$.post(route,{action:'load_controller_table'},function(data){
					$('#controller_table').empty();
					$('#controller_table').append(data);
				});

			});

			$('#action_table').hide();

		});

		$('#btnNewAct').click(function(){
			var c = $('#cbController').val();

			var route = 'language_route/ajax';

			// pre ajax access test
			$.post(route,function(data){
				// route fixing
				if( data.indexOf('<') > -1 ){
					route = 'ajax';
					console.log(route);
				}

				// loading of controller tables
				$.post(route,{action:'load_action_table',controller:c},function(data){
					$('#action_table').empty();
					$('#action_table').append(data);
				});

			});

			$('#action_table').fadeIn();
		});

		$('#cbController').change(function(){
			var c = $(this).val();
			var route = 'language_route/ajax';

			// pre ajax access test
			$.post(route,function(data){
				// route fixing
				if( data.indexOf('<') > -1 ){
					route = 'ajax';
					console.log(route);
				}

				// loading of controller tables
				$.post(route,{action:'load_action_table',controller:c},function(data){
					$('#action_table').empty();
					$('#action_table').append(data);
				});

			});
		});

		$('#addAction').click(function(){
			var c = $('#cbController').val();
			var a = $('#tbNewAction').val();

			var route = 'language_route/ajax';
			
			// pre ajax access test
			$.post(route,function(data){
				// route fixing
				if( data.indexOf('<') > -1 ){
					route = 'ajax';
					console.log(route);
				}

				// loading of action tables
				$.post(route,
					{
						action:'add_action',
						controller:c,
						act:a
					},
					function(data){
						$('#action_table').empty();
						$('#action_table').append(data);
						console.log(data);
					}
				);

			});
		});

		// ARNIE'S ===================================================================

		$('#btnDelAct').click(function(){
			var co = $('#sel').val();

			var route = 'language_route/ajax_arnie';

			$.post(route,function(data){
				if(data.indexOf('<') > -1){
					route = 'next_page/';
				}
				else{
					route = 'language_route/next_page/';
				}

				window.open(route+co,"","width=600,height=400");
			});
		});

		// KARL'S ===================================================================

		$('#selx').change(function(){
			var x = $(this).val();
			$('#myhid').val(x);
		});

		$('#selx').change(function(){
			var controller = $(this).val();
			$('#LanguageRouteIndexForm').attr('action','/cubictalkta/language_route/next_page_update/'+controller);
		});

		$('#selx').change();


	});
</script>
