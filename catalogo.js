let catalogoEditando = null;


async function crearItemCatalogo(){

    let formData = new FormData();

    formData.append("nombre", cat_nombre.value);
    formData.append("tipo", cat_tipo.value);
    formData.append("precio", cat_precio.value);
    formData.append("stock", cat_stock.value);
    formData.append("descripcion", cat_descripcion.value);

    if(cat_imagen.files[0]){
        formData.append("imagen", cat_imagen.files[0]);
    }

    let url = "http://localhost/vetlia/crear_catalogo.php";

    // SI ESTA EDITANDO
    if(catalogoEditando){

        formData.append("id", catalogoEditando);

        url = "http://localhost/vetlia/editar_catalogo.php";
    }

    let r = await fetch(url,{
        method:"POST",
        body:formData
    });

    let data = await r.json();

    if(data.success){

        alert(
            catalogoEditando
            ?
            "Producto actualizado"
            :
            "Producto guardado"
        );

        limpiarFormulario();

        cargarCatalogo();

        catalogoEditando = null;

        btnGuardar.innerText = "Guardar";
    }
}

function limpiarFormulario(){

    cat_nombre.value = "";
    cat_tipo.value = "producto";
    cat_precio.value = "";
    cat_stock.value = "";
    cat_descripcion.value = "";
    cat_imagen.value = "";
}

async function cargarCatalogo(){

    listaCatalogo.style.display = "grid";

    let r = await fetch(
      "http://localhost/vetlia/get_catalogo.php"
    );

    let data = await r.json();

    listaCatalogo.innerHTML = "";

    data.forEach(item => {

        let icono = "📦";

        if(item.tipo == "medicina"){
            icono = "💊";
        }

        if(item.tipo == "servicio"){
            icono = "🩺";
        }

        listaCatalogo.innerHTML += `

            <div class="card">

                ${
                    item.imagen
                    ?
                    `<img src="uploads/${item.imagen}"
                    class="card-img">`
                    :
                    `<div class="emoji">${icono}</div>`
                }

                <h3>${item.nombre}</h3>

                <p>${item.tipo}</p>

                <p>$ ${item.precio}</p>

                <p>Stock: ${item.stock}</p>

                <button onclick="editarCatalogo(${item.id})">
                    Editar
                </button>

                <button onclick="eliminarCatalogo(${item.id})">
                    Eliminar
                </button>

            </div>
        `;
    });
}

async function eliminarCatalogo(id){

    if(!confirm("Eliminar item?")) return;

    let r = await fetch(
      "http://localhost/vetlia/eliminar_catalogo.php?id=" + id
    );

    let data = await r.json();

    if(data.success){

        cargarCatalogo();
    }
}

async function editarCatalogo(id){

    let r = await fetch(
      "http://localhost/vetlia/get_catalogo_by_id.php?id=" + id
    );

    let item = await r.json();

    catalogoEditando = id;

    cat_nombre.value = item.nombre;
    cat_tipo.value = item.tipo;
    cat_precio.value = item.precio;
    cat_stock.value = item.stock;
    cat_descripcion.value = item.descripcion;

    btnGuardar.innerText = "Actualizar";

    window.scrollTo({
        top:0,
        behavior:"smooth"
    });
}

