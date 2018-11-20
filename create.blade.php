@extends ('layouts.app')
@section ('content')
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs_12">
			<h3>Formulario para Vender tu Vehiculo:</h3>
			@if (count($errors)>0)
			<div class="alert alert-danger">
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{$error}}</li>
					@endforeach
				</ul>
			</div>
			@endif

			{!!Form::open(array('url'=>'vistavehiculos','method'=>'POST','autocomplete'=>'off'))!!}
			{{Form::token()}}

			<div class="form-group">
				<label for="contacto">Tu Nombre</label>
				<input type="text" name="contacto" value="{{old('contacto')}}" class="form-control" placeholder="Contacto...">
			</div>

			<div class="form-group">
				<label for="tel_contacto">Tu Telefono</label>
				<input type="text" name="tel_contacto" value="{{old('tel_contacto')}}" class="form-control" placeholder="Telefono...">
			</div>

			<div class="form-group">
				<label for="email_contacto">Tu Email</label>
				<input type="text" name="email_contacto" value="{{old('email_contacto')}}" class="form-control" placeholder="Email...">
			</div>

			<div class="form-group">
				<label for="nombre">Descripcion corta del vehiculo</label>
				<input type="text" name="nombre" value="{{old('nombre')}}" class="form-control" placeholder="Nombre...">
			</div>

			<div class="form-group">
				<label for="marca">Marca</label>
				<input type="text" name="marca" value="{{old('marca')}}" class="form-control" placeholder="Marca...">
			</div>

			<div class="form-group">
				<label for="modelo">Modelo</label>
				<input type="text" name="modelo" value="{{old('modelo')}}" class="form-control" placeholder="Modelo...">
			</div>

			<div class="form-group">
				<label for="linea">Linea</label>
				<input type="text" name="linea" value="{{old('linea')}}" class="form-control" placeholder="Linea...">
			</div>

			<div class="form-group">
				<label for="tipo">Tipo</label>

				<select name="tipo" class="form-control" value="{{ old('tipo') }}">
    				    <option value="Coupe">Coupe</option>
    					<option value="Sedan">Sedan</option>
    				    <option value="Hatchback">Hatchback</option>
    				    <option value="Compacto">Compacto</option>
    				    <option value="Pickup">Pickup</option>
    				    <option value="Wagon">Wagon</option>
    				    <option value="Convertible">Convertible</option>
    				    <option value="Camioneta">Camioneta</option>
    				    <option value="SUV">SUV</option>
    				    <option value="Van">Van</option>
    			</select>

			</div>

			<div class="form-group">
				<label for="origen">Origen</label>
				
				<select name="origen" class="form-control" value="{{ old('origen') }}">
    				    <option value="Agencia">Agencia</option>
    					<option value="Rodado">Rodado</option>
    			</select>

			</div>

			<div class="form-group">
				<label for="precio">Precio</label>Q.
				<input type="text" name="precio" value="{{old('precio')}}" class="form-control" placeholder="Precio..." onkeypress="return isNumber(event)">
			</div>

			<div class="form-group">
				<label for="puertas">Puertas</label>
				<select name="puertas" class="form-control" value="{{ old('puertas') }}">
    				    <option value="1">1</option>
    					<option value="2">2</option>
    					<option value="3">3</option>
    					<option value="4">4</option>
    					<option value="5">5</option>
    					<option value="6">6</option>
    					<option value="7">7</option>
    					<option value="8">8</option>
    					<option value="9">9</option>
    			</select>
			</div>

			<div class="form-group">
				<label for="motor">Motor</label>
				<input type="text" name="motor" value="{{old('motor')}}" class="form-control" placeholder="Motor...">
			</div>

			<div class="form-group">
				<label for="cilindros">Cilindros</label>
				<select name="cilindros" class="form-control" value="{{ old('cilindros') }}">
    				    <option value="1">1</option>
    					<option value="2">2</option>
    					<option value="3">3</option>
    					<option value="4">4</option>
    					<option value="5">5</option>
    					<option value="6">6</option>
    					<option value="7">7</option>
    					<option value="8">8</option>
    					<option value="9">9</option>
    			</select>
			</div>

			<div class="form-group">
				<label for="combustible">Combustible</label>
				
				<select name="combustible" class="form-control" value="{{ old('combustible') }}">
    				    <option value="Gasolina">Gasolina</option>
    					<option value="Diesel">Diesel</option>
    					<option value="Electrico">Electrico</option>
    					<option value="Hibrido">Hibrido</option>
    					<option value="Biocombustible">Biocombustible</option>
    			</select>

			</div>

			<div class="form-group">
				<label for="millas">Millas</label>
				<input type="text" name="millas" value="{{old('millas')}}" class="form-control" placeholder="Millas...">
			</div>

			<div class="form-group">
				<label for="descripcion">Descripcion</label>
				<input type="text" name="descripcion" value="{{old('descripcion')}}" class="form-control" placeholder="Descripcion...">
			</div>

			<div class="form-group">
				<label for="ac">Aire Acondicionado</label>
				<select name="ac" class="form-control" value="{{ old('ac') }}">
						<option value="NO">NO</option>
    				    <option value="SI">SI</option>
    			</select>
			</div>

			<div class="form-group">
				<label for="full_equipo">Full Equipo</label>
				<select name="full_equipo" class="form-control" value="{{ old('full_equipo') }}">
    				    <option value="NO">NO</option>
    				    <option value="SI">SI</option>
    			</select>
			</div>

			<div class="form-group">
				<label for="estado">Estado</label>
				<select name="estado" class="form-control" value="{{ old('estado') }}">
    				    <option value="Nuevo">Nuevo</option>
    				    <option value="Semi-Nuevo">Semi-Nuevo</option>
    				    <option value="Usado">Usado</option>
    			</select>
			</div>

			<div class="form-group">
				<button class="btn btn-default" type="reset"><i class="fa fa-refresh"></i> Reset</button>
				<button class="btn btn-success" type="submit"><i class="fa fa-check-square-o"> Enviar Formulario</i></button>
			</div>

			{!!Form::close()!!}

		</div>
	</div>

	<script type="text/javascript">     
	function isNumber(evt) {
	        evt = (evt) ? evt : window.event;
	        var charCode = (evt.which) ? evt.which : evt.keyCode;
	        if ( (charCode > 31 && charCode < 48) || charCode > 57) {
	            return false;
	        }
	        return true;
	    }
	</script>
@endsection

