@extends('core')

@section('content')
    <div class="d-flex mb-2">
        <h5>Bodegas</h5>
        <button class="btn btn-primary btn-sm ml-auto" onclick="openCreateWarehouseModal()">Crear Bodega</button>
    </div>

    <table class="table table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Estado</th>
                <th>Opciones</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <!-- Modal -->
    <div class="modal fade" id="warehouseModal" tabindex="-1" aria-labelledby="warehouseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="warehouseModalLabel">Detalle de la bodega</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Nombre:</strong> <span id="modalWarehouseNombre"></span></p>
                    <p><strong>Responsable:</strong> <span id="modalWarehouseResponsable"></span></p>
                    <p><strong>Estado:</strong> <span id="modalWarehouseEstado"></span></p>
                    <p><strong>Creada en:</strong> <span id="modalWarehouseCreateAt"></span></p>
                    <p><strong>Actualizada en:</strong> <span id="modalWarehouseUpdateAt"></span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Warehouse Modal -->
    <div class="modal fade" id="createWarehouseModal" tabindex="-1" aria-labelledby="createWarehouseModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createWarehouseModalLabel">Crear Bodega</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="createWarehouseForm">
                        <div class="mb-3">
                            <label for="warehouseNombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="warehouseNombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="warehouseResponsable" class="form-label">Responsable</label>
                            <select class="form-control" id="warehouseResponsable" required>
                                <option value="">Seleccione un responsable</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Crear</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const table = document.querySelector('table');

        axios.get('/api/v1/warehouses').then(response => {
            const warehouses = response.data;
            warehouses.forEach(warehouse => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${warehouse.id}</td>
                    <td>${warehouse.nombre}</td>
                    <td>
                        <span class="badge ${warehouse.estado === 1 ? 'badge-success' : 'badge-danger'}">
                            ${warehouse.estado === 1 ? 'Activo' : 'Inactivo'}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick="show(${warehouse.id})">Ver</button>
                    </td>
                `;
                table.appendChild(tr);
            });
        }).catch(error => {
            console.error(error);
        });

        const show = (id) => {
            axios.get(`/api/v1/warehouses/${id}`)
                .then(response => {
                    const warehouse = response.data;
                    document.getElementById('modalWarehouseNombre').textContent = warehouse.nombre;
                    document.getElementById('modalWarehouseResponsable').textContent = warehouse.responsable.nombre;
                    document.getElementById('modalWarehouseEstado').innerHTML = `<span class="badge ${warehouse.estado === 1 ? 'badge-success' : 'badge-danger'}">${warehouse.estado === 1 ? 'Activo' : 'Inactivo'}</span>`;
                    document.getElementById('modalWarehouseCreateAt').textContent = warehouse.created_at;
                    document.getElementById('modalWarehouseUpdateAt').textContent = warehouse.updated_at;
                    const modal = new bootstrap.Modal(document.getElementById('warehouseModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error(error);
                });
        }

        const openCreateWarehouseModal = () => {
            const modal = new bootstrap.Modal(document.getElementById('createWarehouseModal'));
            modal.show();
        }

        const getUsersList = () => {
            axios.get('/api/v1/users').then(response => {
                const users = response.data;
                const select = document.getElementById('warehouseResponsable');
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

        document.getElementById('createWarehouseForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const nombre = document.getElementById('warehouseNombre').value;
            const responsable = document.getElementById('warehouseResponsable').value;

            axios.post('/api/v1/warehouses', {
                nombre: nombre,
                id_responsable: responsable,
                created_by: responsable, // TODO: He simulado que el usuario responsable de la bodega es el mismo que la crea (Pues no hay autenticación de usuarios)
                estado: 1
            }).then(response => {
                location.reload();
            }).catch(error => {
                console.error(error);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            getUsersList();
        });
    </script>
@endsection
