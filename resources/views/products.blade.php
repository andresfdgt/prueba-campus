@extends('core')

@section('content')
    <div class="d-flex mb-2">
        <h5>Productos</h5>
        <button class="btn btn-primary btn-sm ml-auto" onclick="openCreateWarehouseModal()">Crear Producto</button>
    </div>

    <table class="table table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Opciones</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <!-- Modal for creating a product -->
    <div class="modal fade" id="createProductModal" tabindex="-1" role="dialog" aria-labelledby="createProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createProductModalLabel">Crear Producto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="createProductForm">
                        <div class="form-group">
                            <label for="productName">Nombre</label>
                            <input type="text" class="form-control" id="productName" name="nombre" required>
                        </div>

                        <div class="form-group">
                            <label for="productDescription">Descripción</label>
                            <textarea class="form-control" id="productDescription" name="descripcion" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="initialQuantity">Cantidad Inicial</label>
                            <input type="number" class="form-control" id="initialQuantity" name="cantidad_inicial"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="warehouse">Bodega</label>
                            <select class="form-control" id="warehouse" name="bodega" required>
                                <option value="">Seleccione una bodega</option>
                            </select>
                        </div>
                        {{-- TODO Campo creado para simular el usuario que crea el producto, esto debido a que no hay autenticación --}}
                        <div class="form-group">
                            <label for="createdBy">Creado por</label>
                            <select class="form-control" id="createdBy" name="created_by" required>
                                <option value="">Seleccione un usuario</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Crear</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for showing a product -->
    <div class="modal fade" id="showProductModal" tabindex="-1" role="dialog" aria-labelledby="showProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="showProductModalLabel">Detalles del Producto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Nombre:</strong> <span id="productNameDetail"></span></p>
                    <p><strong>Descripción:</strong> <span id="productDescriptionDetail"></span></p>
                    <p><strong>Estado:</strong> <span id="productStatusDetail"></span></p>
                    <p><strong>Creado en:</strong> <span id="productCreatedAtDetail"></span></p>
                    <p><strong>Actualizado en:</strong> <span id="productUpdatedAtDetail"></span></p>
                    <hr>
                    <div class="row" id="productInventoryDetail">
                        <div class="col-12">
                            <h5>Inventario: <span id="totalStock"></span></h5>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Bodega</th>
                                        <th>Cantidad</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const table = document.querySelector('table');

        axios.get('/api/v1/products').then(response => {
            const products = response.data;
            products.forEach(product => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${product.id}</td>
                    <td>${product.nombre}</td>
                    <td>${product.total}</td>
                    <td>
                        <span class="badge ${product.estado === 1 ? 'badge-success' : 'badge-danger'}">
                            ${product.estado === 1 ? 'Activo' : 'Inactivo'}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="show(${product.id})">Ver</button>
                    </td>
                `;
                table.appendChild(tr);
            });
        }).catch(error => {
            console.error(error);
        });

        const show = (id) => {
            axios.get(`/api/v1/products/${id}`).then(response => {
                const product = response.data;
                openShowProductModal(product);
            }).catch(error => {
                console.error(error);
            });
        }

        const openCreateWarehouseModal = () => {
            $('#createProductModal').modal('show');
        }

        const openShowProductModal = (product) => {
            $('#productNameDetail').text(product.nombre);
            $('#productDescriptionDetail').text(product.descripcion);
            $('#productStatusDetail').html(
                `<span class="badge ${product.estado === 1 ? 'badge-success' : 'badge-danger'}">${product.estado === 1 ? 'Activo' : 'Inactivo'}</span>`
                );
            $('#productCreatedAtDetail').text(product.created_at);
            $('#productUpdatedAtDetail').text(product.updated_at);

            const inventoryTable = document.querySelector('#productInventoryDetail table tbody');
            inventoryTable.innerHTML = '';
            let totalStock = 0;
            product.inventarios.forEach(inventory => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${inventory.bodega.nombre}</td>
                    <td>${inventory.cantidad}</td>
                    <td>${inventory.created_at}</td>
                `;

                totalStock += inventory.cantidad;
                inventoryTable.appendChild(tr);
            });

            $('#totalStock').text(totalStock);
            $('#showProductModal').modal('show');
        }

        document.getElementById('createProductForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            axios.post('/api/v1/products', {
                nombre: formData.get('nombre'),
                descripcion: formData.get('descripcion'),
                estado: 1,
                initial_quantity: formData.get('cantidad_inicial'),
                id_bodega: formData.get('bodega'),
                created_by: formData.get('created_by')
            }).then(response => {
                location.reload();
            }).catch(error => {
                console.error(error);
            });
        });

        const getWarehousesList = () => {
            axios.get('/api/v1/warehouses').then(response => {
                const warehouses = response.data;
                const select = document.getElementById('warehouse');
                warehouses.forEach(warehouse => {
                    const option = document.createElement('option');
                    option.value = warehouse.id;
                    option.textContent = warehouse.nombre;
                    select.appendChild(option);
                });
            }).catch(error => {
                console.error(error);
            });
        }

        const getUsersList = () => {
            axios.get('/api/v1/users').then(response => {
                const users = response.data;
                const select = document.getElementById('createdBy');
                users.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = user.nombre;
                    select.appendChild(option);
                });
            }).catch(error => {
                console.error(error);
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            getWarehousesList();
            getUsersList();
        });
    </script>
@endsection
