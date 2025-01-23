@extends('core')

@section('content')
    <div class="d-flex mb-2">
        <h5>Gestión de inventario</h5>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Agregar unidades a un producto</h5>
                </div>
                <div class="card-body">
                    <form id="addInventoryForm">
                        <div class="form-group">
                            <label for="product">Producto</label>
                            <select class="form-control productList" id="product" name="id_producto" required>
                                <option value="">Seleccione un producto</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="warehouse">Bodega</label>
                            <select class="form-control warehouseList" id="warehouse" name="id_bodega" required>
                                <option value="">Seleccione una bodega</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Cantidad</label>
                            <input type="number" class="form-control" id="quantity" name="cantidad" value="" placeholder="Ingrese la cantidad" required>
                        </div>
                        <div class="form-group">
                            <div class="d-flex justify-content-between">
                                <label for="createdBy">Creado por</label>
                                <span class="text-danger pr-2" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Este campo es utilizado para simular el usuario autenticado">&lowast;</span>
                            </div>
                            <select class="form-control usersList" id="createdBy" name="created_by" required>
                                <option value="">Seleccione un usuario</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="alert d-none" id="addInventoryAlert" role="alert"></div>
                        </div>

                        <button type="submit" class="btn btn-primary">Agregar</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Trasladar unidades entre bodegas</h5>
                </div>
                <div class="card-body">
                    <form id="transferInventoryForm">
                        <div class="form-group">
                            <label for="productMove">Producto</label>
                            <select class="form-control productList" id="productMove" name="id_producto" required>
                                <option value="6">Seleccione un producto</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="warehouseOrigin">Bodega Origen</label>
                            <select class="form-control warehouseList" id="warehouseOrigin" name="id_bodega_origen" required>
                                <option value="4">Seleccione una bodega de origen</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="warehouseDestination">Bodega Destino</label>
                            <select class="form-control warehouseList" id="warehouseDestination" name="id_bodega_destino" required>
                                <option value="3">Seleccione una bodega de destino</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="quantityMove">Cantidad</label>
                            <input type="number" class="form-control" id="quantityMove" name="cantidad" value="" placeholder="Ingrese la cantidad" required>
                        </div>
                        <div class="form-group">
                            <div class="d-flex justify-content-between">
                                <label for="createdByMove">Creado por</label>
                                <span class="text-danger pr-2" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Este campo es utilizado para simular el usuario autenticado">&lowast;</span>
                            </div>
                            <select class="form-control usersList" id="createdByMove" name="created_by" required>
                                <option value="1">Seleccione un usuario</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <div class="alert d-none" id="transferInventoryAlert" role="alert"></div>
                        </div>

                        <button type="submit" class="btn btn-primary">Trasladar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const getUsersList = () => {
            axios.get('/api/v1/users').then(response => {
                const users = response.data;
                const selects = document.querySelectorAll('.usersList');
                selects.forEach(select => {
                    users.forEach(user => {
                        const option = document.createElement('option');
                        option.value = user.id;
                        option.textContent = user.nombre;
                        select.appendChild(option);
                    });
                });
            }).catch(error => {
                console.error(error);
            });
        }

        const getWarehousesList = () => {
            axios.get('/api/v1/warehouses').then(response => {
                const warehouses = response.data;
                const selects = document.querySelectorAll('.warehouseList');
                selects.forEach(select => {
                    warehouses.forEach(warehouse => {
                        const option = document.createElement('option');
                        option.value = warehouse.id;
                        option.textContent = warehouse.nombre;
                        select.appendChild(option);
                    });
                });
            }).catch(error => {
                console.error(error);
            });
        }

        const getProductsList = () => {
            axios.get('/api/v1/products').then(response => {
                const products = response.data;
                const selects = document.querySelectorAll('.productList');
                selects.forEach(select => {
                    products.forEach(product => {
                        const option = document.createElement('option');
                        option.value = product.id;
                        option.textContent = product.nombre;
                        select.appendChild(option);
                    });
                });
            }).catch(error => {
                console.error(error);
            });
        }

        const resetForm = () => {
            document.getElementById('addInventoryForm').reset();
            document.getElementById('transferInventoryForm').reset();
        }

        document.addEventListener('DOMContentLoaded', function() {
            getProductsList();
            getWarehousesList();
            getUsersList();
        });

        document.getElementById('addInventoryForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            axios.post('/api/v1/inventories', {
                id_producto: formData.get('id_producto'),
                id_bodega: formData.get('id_bodega'),
                cantidad: formData.get('cantidad'),
                created_by: formData.get('created_by')
            }).then(response => {
                let alert = document.getElementById('addInventoryAlert');
                alert.classList.remove('d-none');
                alert.classList.remove('alert-danger');
                alert.classList.add('alert-success');
                alert.textContent = 'Inventario agregado exitosamente.';
                resetForm();
            }).catch(error => {
                let alert = document.getElementById('addInventoryAlert');
                alert.classList.remove('d-none');
                alert.classList.remove('alert-success');
                alert.classList.add('alert-danger');
                alert.textContent = 'Error al agregar el inventario.';
                console.error(error);
                resetForm();
            });
        });

        document.getElementById('transferInventoryForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            axios.post('/api/v1/inventories/transfer', {
                id_producto: formData.get('id_producto'),
                id_bodega_origen: formData.get('id_bodega_origen'),
                id_bodega_destino: formData.get('id_bodega_destino'),
                cantidad: formData.get('cantidad'),
                created_by: formData.get('created_by')
            }).then(response => {
                let alert = document.getElementById('transferInventoryAlert');
                alert.classList.remove('d-none');
                alert.classList.remove('alert-danger');
                alert.classList.add('alert-success');
                alert.textContent = 'Inventario trasladado exitosamente.';
                resetForm();
            }).catch(error => {
                let alert = document.getElementById('transferInventoryAlert');
                alert.classList.remove('d-none');
                alert.classList.remove('alert-success');
                alert.classList.add('alert-danger');
                alert.textContent = error.response.data.message ?? 'Error al trasladar el inventario.';
                console.error(error);
                resetForm();
            });
        });
    </script>
@endsection
