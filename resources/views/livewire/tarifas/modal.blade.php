<div class="modal fade" id="modalTarifa" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Descripción del Vehículo</h5>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <form>
                            <div class="row">
                                <div class="form-group col-lg-4 col-md-4 col-sm-12">
                                    <label for="tiempo">Tiempo</label>
                                    <select id="tiempo" class="form-control text-center">
                                        <option value="Elegir">Elegir</option>
                                        <option value="Hora">Hora</option>
                                        <option value="Dia">Dia</option>
                                        <option value="Semana">Semana</option>
                                        <option value="Mes">Mes</option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-4 col-md-4 col-sm-12">
                                    <label >Tipo</label>
                                    <select id="tipo" class="form-control text-center">
                                        <option value="Elegir" disabled="">Elegir</option>
                                        @foreach($tipos as $t)
                                        <option value="{{ $t->id }}" >
                                            {{ $t->descripcion}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-4 col-md-4 col-sm-12">
                                    <label >Costo</label>
                                    <input id="costo" type="text" class="form-control text-center numeric"  placeholder="10.00">
                                </div>
                                <div class="form-group col-lg-8 col-sm-12 mb-8">
                                    <label >Descripción</label>
                                    <input id="descripcion" type="text" class="form-control"  placeholder="Tarifa Hora Coche">
                                </div>
                                <div class="form-group col-lg-4 col-md-4 col-sm-12">
                                    <label >Jerarquía</label>
                                    <input id="jerarquia" type="number" class="form-control text-center" disabled value="{{ $jerarquia }}" >
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cancelar</button>
				<button type="button" onclick="save()" class="btn btn-primary saveTarifa"> Guardar</button>
            </div>
        </div>
    </div>
</div>
