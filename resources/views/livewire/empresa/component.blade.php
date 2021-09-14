<div class="widget-content-area">
	<div class="widget-one">
		<div class="row">

			 @include('common.alerts')

			<div class="col-12">
				<h4 class="text-center">Datos de la Empresa</h4>
			</div>
			<div class="form-group col-sm-12">
				<label >Nombre</label>
				<input wire:model.lazy="nombre" type="text" class="form-control text-left" >
				@error('nombre') <span class="text-danger">{{ $message }}</span> @enderror
			</div>
			<div class="form-group col-lg-4 col-md-4 col-sm-12">
				<label >Teléfono</label>
				<input wire:model.lazy="telefono" maxlength="10" type="text" class="form-control text-center"  >
				@error('telefono') <span class="text-danger">{{ $message }}</span> @enderror
			</div>
			<div class="form-group col-lg-4 col-md-4 col-sm-12">
				<label >Email</label>
				<input wire:model.lazy="email" maxlength="55" type="text" class="form-control text-center"  >
				@error('email') <span class="text-danger">{{ $message }}</span> @enderror
			</div>
			<div class="form-group col-lg-4 col-md-4 col-sm-12">
				<label >Logo</label>
				<input  type="file" class="form-control" id ="image"
				wire:change="$emit('fileChoosen',this)" accept="image/x-png,image/gif,image/jpeg">
			</div>
			<div class="form-group col-sm-12">
				<label >Dirección</label>
				<input wire:model.lazy="direccion" type="text" class="form-control text-left"  >
				@error('direccion') <span class="text-danger">{{ $message }}</span> @enderror
			</div>
			<div class="col-12">
                @can('empresa_create')
				<button type="button"
				wire:click="Guardar()"
				class="btn btn-primary ml-2">
				<i class="mbri-success"></i> Guardar
				</button>
                @endcan
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	document.addEventListener('DOMContentLoaded', function () {

		window.livewire.on('fileChoosen', () => {

			let inputField = document.getElementById('image')
			let file = inputField.files[0]
			let reader = new FileReader()
			reader.onloadend = () => {
				window.livewire.emit('logoUpload', reader.result)
			}
			reader.readAsDataURL(file)
		})
	})

</script>
