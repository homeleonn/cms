<div class="extra-fields side-block">
	<div class="block-title">Произвольные поля</div>
	<div class="block-content">
		<div class="new_extra_fields">
			<div class="row fields_caption none" style="background: lightgray; padding: 8px; margin: 10px 0;">
				<div class="col-md-4 center">Имя</div>
				<div class="col-md-8 center">Значение</div>
			</div>
			<?php 
				if(isset($data['meta_data'])){
					$index = 0;
					foreach($data['meta_data'] as $name => $value)
						getExtraField($index++, $name, $value);
				}
			?>
		</div>
		<div class="mtop10">Добавить новое поле:</div>
		<div class="extra_table">
			<div class="row" style="background: lightgray; padding: 8px; margin: 10px 0;">
				<div class="col-md-4 center">Имя</div>
				<div class="col-md-8 center">Значение</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<select id="select_extra_name" class="w100">
						<option value="0">-- Выберите --</option>
						<?php 
						if(isset($data['extra_fields_list']) && $data['extra_fields_list']){
							$i = 1;
							foreach($data['extra_fields_list'] as $field){
								echo '<option value="',($i++),'">',$field,'</option>';
							}
						}
								
						?>
					</select>
					<input type="text" id="input_extra_name" class="w100 none"><br>
					<a href="#" id="init_new_extra" class="mtop10">Введите новое</a>
				</div>
				<div class="col-md-8">
					<textarea id="extra_value_editor" class="w100" rows="2"></textarea>
				</div>
			</div>
		</div>
		<input class="mtop10" type="button" id="add_new_extra_name" value="Добавить произвольное поле">
		<div class="b mtop10">Произвольные поля позволяют добавлять к записям метаданные, которые вы можете использовать в своей теме.</div>
	</div>
</div>