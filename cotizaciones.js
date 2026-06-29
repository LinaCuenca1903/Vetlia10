// cotizaciones.js

let carrito = [];

let cotizacionEditando = null;

let catalogoCompleto = [];



async function cargarMascotas(){

    let r = await fetch(
      "http://localhost/vetlia/get_pacientes.php"
    );

    let data = await r.json();

    cot_mascota.innerHTML =
    `<option value="">Seleccionar mascota</option>`;

    data.forEach(m => {

        cot_mascota.innerHTML += `
            <option value="${m.id}">
                ${m.nombre}
            </option>
        `;
    });
}



async function cargarTutores(){

    let r = await fetch(
      "http://localhost/vetlia/get_tutores.php"
    );

    let data = await r.json();

    cot_tutor.innerHTML = `
        <option value="">
            Seleccionar tutor
        </option>
    `;

    data.forEach(t => {

        cot_tutor.innerHTML += `
            <option value="${t.id}">
                ${t.nombre}
            </option>
        `;

    });

}



async function cargarMascotasPorTutor(){

    let tutorId = cot_tutor.value;

    let r = await fetch(
      "http://localhost/vetlia/get_pacientes.php"
    );

    let data = await r.json();

    cot_mascota.innerHTML =
    `<option value="">Seleccionar mascota</option>`;

    listaCotizaciones.innerHTML = "";

    data.forEach(m => {

        if(
            tutorId == ""
            ||
            m.tutor_id == tutorId
        ){

            cot_mascota.innerHTML += `
                <option value="${m.id}">
                    ${m.nombre}
                </option>
            `;
        }

    });

}



async function cargarCatalogo(){

    let r = await fetch(
      "http://localhost/vetlia/get_catalogo.php"
    );

    let data = await r.json();

    console.log(data);

    catalogoCompleto = data;

    renderCatalogo(data);
}



function renderCatalogo(data){

    catalogoContainer.innerHTML = "";

    data.forEach(item => {

        let tipo =
        String(item.tipo)
        .toLowerCase()
        .trim();

        let icono = "📦";

        if(tipo.includes("medicina")){
            icono = "💊";
        }

        if(tipo.includes("servicio")){
            icono = "🩺";
        }

        if(tipo.includes("producto")){
            icono = "🛍️";
        }

        let nombreSeguro =
item.nombre.replace(/'/g, "\\'");

catalogoContainer.innerHTML += `

    <div class="card">

        ${
            item.imagen
            ?
            `<img src="uploads/${item.imagen}">`
            :
            `<div style="font-size:80px">${icono}</div>`
        }

        <h3>${item.nombre}</h3>

        <p>${item.tipo}</p>

        <p>$ ${item.precio}</p>

        <input
            type="number"
            id="cantidad_${item.id}"
            min="1"
            value="1"
        >

        <button onclick="agregarItem(
            ${item.id},
            '${nombreSeguro}',
            ${item.precio}
        )">

            Agregar

        </button>

    </div>

        `;
    });
}



function filtrarCatalogo(){

    let texto =
    document.getElementById(
        "buscarCatalogo"
    ).value.toLowerCase().trim();

    let categoria =
    document.getElementById(
        "filtroCategoria"
    ).value.toLowerCase().trim();

    let filtrados = catalogoCompleto.filter(item => {

        let nombre =
        String(item.nombre)
        .toLowerCase()
        .trim();

        let tipo =
        String(item.tipo)
        .toLowerCase()
        .trim();

        let coincideTexto =
        nombre.includes(texto);

        let coincideCategoria =
        categoria === ""
        ||
        tipo.includes(categoria);

        return coincideTexto
        &&
        coincideCategoria;

    });

    console.log(filtrados);

    renderCatalogo(filtrados);
}



function agregarItem(id,nombre,precio){

    let cantidad =
    document.getElementById(
        `cantidad_${id}`
    ).value;

    cantidad = parseInt(cantidad);

    let subtotal = cantidad * precio;

    carrito.push({

        catalogo_id:id,
        nombre:nombre,
        precio:precio,
        cantidad:cantidad,
        subtotal:subtotal

    });

    renderCarrito();
}



function renderCarrito(){

    detalleCotizacion.innerHTML = "";

    let total = 0;

    carrito.forEach((item,index) => {

        total += item.subtotal;

        detalleCotizacion.innerHTML += `

            <tr>

                <td>${item.nombre}</td>

                <td>

                    <input
                        type="number"
                        min="1"
                        value="${item.cantidad}"
                        onchange="editarCantidad(${index},this.value)"
                    >

                </td>

                <td>$ ${item.precio}</td>

                <td>$ ${item.subtotal}</td>

                <td>

                    <button onclick="eliminarItem(${index})">
                        X
                    </button>

                </td>

            </tr>
        `;
    });

    totalCotizacion.innerText =
    "Total: $" + total;
}



function editarCantidad(index,nuevaCantidad){

    nuevaCantidad = parseInt(nuevaCantidad);

    carrito[index].cantidad = nuevaCantidad;

    carrito[index].subtotal =
    nuevaCantidad * carrito[index].precio;

    renderCarrito();
}



function eliminarItem(index){

    carrito.splice(index,1);

    renderCarrito();
}



async function guardarCotizacion(){

    let user =
    JSON.parse(localStorage.getItem("user"));

    if(!cot_mascota.value){

        alert("Selecciona mascota");

        return;
    }

    if(carrito.length == 0){

        alert("Agrega items");

        return;
    }

    let total = 0;

    carrito.forEach(i => {
        total += i.subtotal;
    });

    let url =
    cotizacionEditando
    ?
    "http://localhost/vetlia/editar_cotizacion_vet.php"
    :
    "http://localhost/vetlia/crear_cotizacion.php";

    let bodyData = {

        mascota_id:cot_mascota.value,

        usuario_id:user.id,

        estado:cot_estado.value,

        total:total,

        items:carrito
    };

    if(cotizacionEditando){

        bodyData.id = cotizacionEditando;

    }

    let r = await fetch(

      url,

      {
        method:"POST",

        headers:{
            "Content-Type":"application/json"
        },

        body:JSON.stringify(bodyData)
      }

    );

    let data = await r.json();

    if(data.success){

        alert(

            cotizacionEditando
            ?
            "Cotización modificada"
            :
            "Cotización guardada"

        );

        carrito = [];

        renderCarrito();

        cot_mascota.value = "";

        cotizacionEditando = null;

        let btnEditar =
        document.getElementById(
            "btnEditarCotizacion"
        );

        if(btnEditar){

            btnEditar.style.display = "none";

        }

        document
        .querySelectorAll("input[type='number']")
        .forEach(i => {
            i.value = 1;
        });

        cargarCotizaciones();
    }
}



async function cargarCotizaciones(){

    if(!cot_mascota.value){

        listaCotizaciones.innerHTML = "";

        return;
    }

    let r = await fetch(

      "http://localhost/vetlia/get_cotizaciones.php?mascota_id="
      + cot_mascota.value

    );

    let data = await r.json();

    listaCotizaciones.innerHTML = "";

    data.forEach(c => {

        listaCotizaciones.innerHTML += `

            <div class="card">

                <h3>
                    Cotización #${c.id}
                </h3>

                <p>
                    Fecha: ${c.fecha}
                </p>

                <p>
                    Total: $${c.total}
                </p>

                <p>
                    Estado: ${c.estado}
                </p>

                <button onclick="visualizarCotizacion(${c.id})">

                    Ver / Modificar

                </button>

                <button onclick="descargarPDF(${c.id})">

                    PDF

                </button>

                <input
                    type="email"
                    id="correo_${c.id}"
                    placeholder="Correo"
                >

                <button onclick="enviarCorreo(${c.id})">

                    Enviar PDF

                </button>

            </div>
        `;
    });
}



async function visualizarCotizacion(id){

    cotizacionEditando = id;

    // MOSTRAR BOTON EDITAR
    let btnEditar =
    document.getElementById(
        "btnEditarCotizacion"
    );

    if(btnEditar){

        btnEditar.style.display =
        "inline-block";

    }

    // CONSULTAR DETALLE
    let r = await fetch(

      "http://localhost/vetlia/get_detalle_cotizacion_vet.php?id="
      + id

    );

    let data = await r.json();

    console.log(data);

    // LIMPIAR CARRITO
    carrito = [];

    // AGREGAR ITEMS
    if(data.items){

        data.items.forEach(i => {

            carrito.push({

                catalogo_id:i.catalogo_id,

                nombre:i.nombre,

                precio:parseFloat(i.precio),

                cantidad:parseInt(i.cantidad),

                subtotal:
                parseFloat(i.precio)
                *
                parseInt(i.cantidad)

            });

        });

    }

    // RENDERIZAR TABLA
    renderCarrito();

    // CARGAR ESTADO
    if(data.estado){

        cot_estado.value =
        data.estado;

    }

    // CARGAR MASCOTA
    if(data.mascota_id){

        cot_mascota.value =
        data.mascota_id;

    }

    // IR ARRIBA
    window.scrollTo({

        top:0,

        behavior:"smooth"

    });

}



function descargarPDF(id){

    window.open(

      "http://localhost/vetlia/generar_pdf.php?id="
      + id,

      "_blank"

    );
}



async function enviarCorreo(id){

    let correo =
    document.getElementById(
        "correo_" + id
    ).value;

    if(!correo){

        alert("Ingresa correo");

        return;
    }

    let r = await fetch(
      "http://localhost/vetlia/enviar_cotizacion.php",
      {
        method:"POST",

        headers:{
            "Content-Type":"application/json"
        },

        body:JSON.stringify({
            id:id,
            correo:correo
        })
      }
    );

    let data = await r.json();

    if(data.success){

        alert("Correo enviado");

    }
}



cargarTutores();
cargarCatalogo();
cargarMascotas();