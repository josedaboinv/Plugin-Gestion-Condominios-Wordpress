<?php

function agregar_estilo_personalizado() {

    $css_url = plugins_url('condo.css', __FILE__);

    wp_enqueue_style('condo', $css_url, array(), '1.0', 'all');
}
add_action('wp_enqueue_scripts', 'agregar_estilo_personalizado',100);

function enqueue_jquery() {
    wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'enqueue_jquery');

## ESTE SE USA PARA TODO LO QUE USE AJAX
function enqueue_my_ajax_script() {

    wp_register_script( 'infocm-script', plugin_dir_url( __DIR__ ) . 'condosystem/infocm.js', array('jquery'), '1.0', true );

    $script_data_array = array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),

    );

    wp_localize_script( 'infocm-script', 'cm_ajax_object', $script_data_array );

    wp_enqueue_script( 'infocm-script' );
}
add_action( 'wp_enqueue_scripts', 'enqueue_my_ajax_script' );

function mostrar_contenido_restringido($atts, $content = null) {

    if (!is_user_logged_in()) {
        return '<p>Debes iniciar sesión para ver este contenido.</p>' . wp_login_form();
    }

    if (!current_user_can('editor')) {
        return '<p>No tienes permisos para ver este contenido.</p>';
    }

    return do_shortcode($content);
}

add_shortcode('contenido_restringido', 'mostrar_contenido_restringido');

add_action('wp_ajax_my_action', 'my_action');
add_action('wp_ajax_nopriv_my_action', 'my_action');
function my_action() {

	if ( !isset($_POST['owner_nonce']) || ! wp_verify_nonce( $_POST['owner_nonce'], 'insert_owner' ) ) {
         die ( 'Acceso no autorizado! '.$_POST['owner_nonce']);
    }

		$nombre= sanitize_text_field($_POST['nombre']);
		$apellido = sanitize_text_field($_POST['apellido']);
		$rut= sanitize_text_field($_POST['rut']);
		$telf = sanitize_text_field($_POST['telefono']);
		$email = sanitize_email($_POST['email']);
		$depa = sanitize_text_field($_POST['depa']);
		$torre = sanitize_text_field($_POST['torre']);
		$parking = sanitize_text_field($_POST['parking']);
		$bodega = sanitize_text_field($_POST['bodega']);

    global $wpdb;
    $table_name = $wpdb->prefix . 'cm_owners';
		$result=
		$wpdb->insert(
        $table_name,
        array(
            'nombre'   => $nombre,
            'apellido' => $apellido,
            'rut'      => $rut,
            'telefono'     => $telf,
            'email'    => $email,
            'depa'     => $depa,
            'torre'    => $torre,
            'parking'  => $parking,
			'bodega'   => $bodega
		),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')

    );
	;

	if($result === false)
	{
		$response=0;
	}else
	{
		$response=1;
	}

    echo $response;

    wp_die();
}

function render_propietarios_form() {

	if(  limit_the_user('gerente')===true || limit_the_user('administrator')===true)
	{

    ob_start(); 

    ?>

   <div class="container"> <div class="row justify-content-center align-items-center" >

        <form action="procesar_formulario.php" id="owner-form" method="POST" class="col-12 col-md-6">
		 <h2>Formulario de Propietarios</h2>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="apellido" class="form-label">Apellido:</label>
                <input type="text" class="form-control" id="apellido" name="apellido" required>
            </div>
            <div class="mb-3">
                <label for="rut" class="form-label">RUT:</label>
				<input type="text" id="rut" name="rut" class="rut-input form-control" placeholder="Ejemplo: 12345678-9" required>

				<div id="rut-message" class="rut-message"></div>
				<div id="rut-error" class="rut-error"></div>	
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono:</label>
                <input type="text" class="form-control" id="telefono" name="telefono" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="torre" class="form-label">Torre:</label>
                <input type="text" class="form-control" id="torre" name="torre" required>
            </div>
            <div class="mb-3">
                <label for="depa" class="form-label">Departamento:</label>
                <input type="text" class="form-control" id="depa" name="depa" required>
            </div>
            <div class="mb-3">
                <label for="parking" class="form-label">Parking:</label>
                <input type="text" class="form-control" id="parking" name="parking" required>
            </div>
            <div class="mb-3">
                <label for="bodega" class="form-label">Bodega:</label>
                <input type="text" class="form-control" id="bodega" name="bodega" required>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>

			<?php 

				wp_nonce_field('insert_owner', 'owner_nonce'); 
			?>
        </form>
    </div>
    <?php

    return ob_get_clean(); 
	}
}
add_shortcode('prop-form', 'render_propietarios_form');

function mostrar_owners_shortcode() {
	if(limit_the_user('guardias')===true ||  limit_the_user('gerente')===true || limit_the_user('administrator')===true)
	{

    global $wpdb;
    $table_name = $wpdb->prefix . 'cm_owners';

    $owners = $wpdb->get_results("SELECT * FROM $table_name");

    ob_start(); 

    ?>
    <div class="table-responsive">
        <table class="table table-striped ">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>RUT</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Torre</th>
                    <th>Depa</th>
                    <th>Parking</th>
                    <th>Bodega</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($owners as $owner) : ?>
                    <tr>
                        <td><?php echo $owner->id; ?></td>
                        <td><?php echo $owner->nombre; ?></td>
                        <td><?php echo $owner->apellido; ?></td>
                        <td><?php echo $owner->rut; ?></td>
                        <td><?php echo $owner->telefono; ?></td>
                        <td><?php echo $owner->email; ?></td>
                        <td><?php echo $owner->torre; ?></td>
                        <td><?php echo $owner->depa; ?></td>
                        <td><?php echo $owner->parking; ?></td>
                        <td><?php echo $owner->bodega; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    return ob_get_clean(); 
	}
}

function register_mostrar_owners_shortcode() {
    add_shortcode('mostrar_owners', 'mostrar_owners_shortcode');
}
add_action('init', 'register_mostrar_owners_shortcode');

############################################################
############### MOSTRAR DUEÑOS POR PAGINACION ################
#############################################################

function mostrar_owners_shortcode2($atts) {
	if(limit_the_user('guardias')===true ||  limit_the_user('gerente')===true || limit_the_user('administrator')===true)
	{

    $registros_por_pagina = 10;

    $pagina = (isset($_POST['pagina'])) ? $_POST['pagina'] : 1;

    global $wpdb;
    $table_name = $wpdb->prefix . 'cm_owners';

    $offset = ($pagina - 1) * $registros_por_pagina;

    $query = $wpdb->prepare("SELECT * FROM $table_name LIMIT %d, %d", $offset, $registros_por_pagina);
    $results = $wpdb->get_results($query);

    $output = '<div class="table-responsive"><table class="table table-striped " id="owners-table" >';
    $output .= '<thead><tr><th>ID</th><th>Nombre</th><th>Apellido</th><th>RUT</th><th>Telefono</th><th>Email</th><th>Torre</th><th>Depa</th><th>Parking</th><th>Bodega</th></tr></thead>';
    $output .= '<tbody>';

    foreach ($results as $row) {
        $output .= '<tr>';
        $output .= '<td>' . $row->id . '</td>';
        $output .= '<td>' . $row->nombre . '</td>';
        $output .= '<td>' . $row->apellido . '</td>';
        $output .= '<td>' . $row->rut . '</td>';
        $output .= '<td>' . $row->telefono . '</td>';
        $output .= '<td>' . $row->email . '</td>';
        $output .= '<td>' . $row->torre . '</td>';
        $output .= '<td>' . $row->depa . '</td>';
        $output .= '<td>' . $row->parking . '</td>';
        $output .= '<td>' . $row->bodega . '</td>';
        $output .= '</tr>';
    }

    $output .= '</tbody>';
    $output .= '</table></div>';

    $total_registros = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");
    $total_paginas = ceil($total_registros / $registros_por_pagina);

    $output .= '<div class="pagination">';
    for ($i = 1; $i <= $total_paginas; $i++) {
        $output .= '<a class="page-link" href="#" data-pagina="' . $i . '">' . $i . '</a>';
    }
    $output .= '</div>';

    return $output;

	}
}

add_shortcode('mostrar_owners2', 'mostrar_owners_shortcode2');

#####################################################################
#################FUNCION AJAX PARA CARGAR POR PAGINA A LOS DUEÑOS

function load_owners_callback() {

    $pagina = (isset($_POST['pagina'])) ? $_POST['pagina'] : 1;

    $registros_por_pagina = 10;
    $offset = ($pagina - 1) * $registros_por_pagina;

    global $wpdb;
    $table_name = $wpdb->prefix . 'cm_owners';

    $query = $wpdb->prepare("SELECT * FROM $table_name LIMIT %d, %d", $offset, $registros_por_pagina);
    $results = $wpdb->get_results($query);

    $output = '<div class=" table-responsive"><table class="table table-striped"  >';
    $output .= '<thead><tr><th>ID</th><th>Nombre</th><th>Apellido</th><th>RUT</th><th>Telefono</th><th>Email</th><th>Torre</th><th>Depa</th><th>Parking</th><th>Bodega</th></tr></thead>';
    $output .= '<tbody>';

    foreach ($results as $row) {
        $output .= '<tr>';
        $output .= '<td>' . $row->id . '</td>';
        $output .= '<td>' . $row->nombre . '</td>';
        $output .= '<td>' . $row->apellido . '</td>';
        $output .= '<td>' . $row->rut . '</td>';
        $output .= '<td>' . $row->telefono . '</td>';
        $output .= '<td>' . $row->email . '</td>';
        $output .= '<td>' . $row->torre . '</td>';
        $output .= '<td>' . $row->depa . '</td>';
        $output .= '<td>' . $row->parking . '</td>';
        $output .= '<td>' . $row->bodega . '</td>';
        $output .= '</tr>';
    }

    $output .= '</tbody>';
    $output .= '</table></div>';

    echo $output;

    wp_die();
}

add_action('wp_ajax_load_owners', 'load_owners_callback');
add_action('wp_ajax_nopriv_load_owners', 'load_owners_callback');

########################################################################
#######################3BYSCADOR#######################################

function buscar_rut_shortcode() {
    if(limit_the_user('guardias')===true ||  limit_the_user('gerente')===true || limit_the_user('administrator')===true)
	{

	ob_start(); 

    ?>
	<div class="container"> <div class="row justify-content-center align-items-center">
    <form id="buscar-form" class="col-12 col-md-6">
		<h3>Ingrese el RUT</h3>
        <label for="rut" class="form-label"> </label>
        <input type="text" id="rut" name="rut" class="rut-input form-control" placeholder="Ejemplo: 12345678-9" required>
		<div id="rut-message" class="rut-message"></div>
		<div id="rut-error" class="rut-error form-label"></div>
        <input type="submit" class="btn btn-primary" value="Buscar">
    </form>
	</div></div>
    <div id="search-result"></div>
    <?php

    return ob_get_clean(); 
	}
}

add_shortcode('buscar_rut', 'buscar_rut_shortcode');

function buscar_rut_callback() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cm_owners';

    $rut = sanitize_text_field($_POST['rut']);

    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE rut = %s", $rut);
    $results = $wpdb->get_results($query);

    if (count($results) > 0) {

        echo '<table class="table">';
        echo '<thead><tr><th>Nombre</th><th>Apellido</th><th>Rut</th><th>Teléfono</th><th>Email</th><th>Torre</th><th>Depa</th><th>Parking</th><th>Bodega</th></tr></thead>';
        echo '<tbody>';

        foreach ($results as $row) {
            echo '<tr>';
            echo '<td>' . esc_html($row->nombre) . '</td>';
            echo '<td>' . esc_html($row->apellido) . '</td>';
            echo '<td>' . esc_html($row->rut) . '</td>';
            echo '<td>' . esc_html($row->telefono) . '</td>';
            echo '<td>' . esc_html($row->email) . '</td>';
            echo '<td>' . esc_html($row->torre) . '</td>';
            echo '<td>' . esc_html($row->depa) . '</td>';
            echo '<td>' . esc_html($row->parking) . '</td>';
            echo '<td>' . esc_html($row->bodega) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {

        echo 'Rut no encontrado.';
    }

    wp_die();
}

add_action('wp_ajax_buscar_rut', 'buscar_rut_callback');
add_action('wp_ajax_nopriv_buscar_rut', 'buscar_rut_callback');

##-----------------------------------------------------
function buscar_rent_shortcode() {
    ob_start(); 
	if(limit_the_user('guardia')===true ||  limit_the_user('gerente')===true || limit_the_user('administrator')===true)
	{
    ?>
	<div class="container"> <div class="row justify-content-center align-items-center">
    <form id="buscar-form-rent" class="col-12 col-md-6">
		<h3>Ingrese el RUT</h3>
        <label for="rut" class="form-label"> </label>
        <input type="text" id="rut" name="rut" class="rut-input form-control" placeholder="Ejemplo: 12345678-9" required>
		<div id="rut-message" class="rut-message"></div>
		<div id="rut-error" class="rut-error form-label"></div>
        <input type="submit" class="btn btn-primary" value="Buscar">
    </form>
	</div></div> 
    <div id="search-result"></div>
    <?php

    return ob_get_clean(); 
	}
}

add_shortcode('buscar_rent', 'buscar_rent_shortcode');

########################################################################
###################### INSERTAR ARRIENDO POR DIA #############################
########################################################################

function insert_rent_data() {

	if ( !isset($_POST['owner_nonce']) || ! wp_verify_nonce( $_POST['owner_nonce'], 'insert_owner' ) ) {

		 echo "<h1>ERROR".$_POST['nombre_rent']."</h1>";
		 die ( 'Acceso no autorizado! '.$_POST['owner_nonce']);
    }

		$nombre_rent = sanitize_text_field($_POST['nombre_rent']);
		$apellido_rent = sanitize_text_field($_POST['apellido_rent']);
		$rut_rent = sanitize_text_field($_POST['rut_rent']);
		$telf_rent = sanitize_text_field($_POST['telf_rent']);
		$email_rent = sanitize_email($_POST['email_rent']);
		$dia_ini = sanitize_text_field($_POST['dia_ini']);
		$dia_fin = sanitize_text_field($_POST['dia_fin']);
		$depa_rent = sanitize_text_field($_POST['depa_rent']);
		$torre_rent = sanitize_text_field($_POST['torre_rent']);
		$esta_rent = sanitize_text_field($_POST['esta_rent']);
		$patent_rent = sanitize_text_field($_POST['patent_rent']);

    global $wpdb;
    $table_name = $wpdb->prefix . 'cm_rents';

    $wpdb->insert(
        $table_name,
        array(
            'nombre_rent'   => $nombre_rent,
            'apellido_rent' => $apellido_rent,
            'rut_rent'      => $rut_rent,
            'telf_rent'     => $telf_rent,
            'email_rent'    => $email_rent,
            'dia_ini'       => $dia_ini,
            'dia_fin'       => $dia_fin,
            'depa_rent'     => $depa_rent,
            'torre_rent'    => $torre_rent,
            'esta_rent'     => $esta_rent,
            'patent_rent'   => $patent_rent
		),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')

    );

    wp_send_json_success('Data inserted successfully');
}

add_action('wp_ajax_insert_rent_data', 'insert_rent_data');
add_action('wp_ajax_nopriv_insert_rent_data', 'insert_rent_data');

function render_insert_arriendo() {

	if(limit_the_user('guardias')===true || limit_the_user('administrator')===true)
	{

    ob_start(); 

    ?>
	<div class="container"> <div class="row justify-content-center align-items-center" >

    <form id="rentday" method="POST" class="col-12 col-md-6">
	<h2>Registro de Arriendo por días</h2>
          <div class="mb-3">
            <label for="nombre_rent" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre_rent" name="nombre_rent" required>
        </div>
        <div class="mb-3">
            <label for="apellido_rent" class="form-label">Apellido</label>
            <input type="text" class="form-control" id="apellido_rent" name="apellido_rent" required>
        </div>
        <div class="mb-3">
            <label for="rut_rent" class="form-label">Rut</label>
            <input type="text" class="form-control" id="rut_rent" name="rut_rent" required>
			<div id="rut-message" class="rut-message"></div>
			<div id="rut-error" class="rut-error"></div>	
        </div>
        <div class="mb-3">
            <label for="telf_rent" class="form-label">Teléfono</label>
            <input type="tel" class="form-control" id="telf_rent" name="telf_rent" required>
        </div>
        <div class="mb-3">
            <label for="email_rent" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" id="email_rent" name="email_rent" required>
        </div>
        <div class="mb-3">
            <label for="dia_ini" class="form-label">Fecha de Inicio</label>
            <input type="date" class="form-control" id="dia_ini" name="dia_ini" required>
        </div>
        <div class="mb-3">
            <label for="dia_fin" class="form-label">Fecha de Fin</label>
            <input type="date" class="form-control" id="dia_fin" name="dia_fin" required>
        </div>
        <div class="mb-3">
            <label for="depa_rent" class="form-label">Departamento</label>
            <input type="text" class="form-control" id="depa_rent" name="depa_rent" required>
        </div>
        <div class="mb-3">
            <label for="torre_rent" class="form-label">Torre</label>
            <input type="text" class="form-control" id="torre_rent" name="torre_rent" required>
        </div>
        <div class="mb-3">
            <label for="esta_rent" class="form-label">Estacionamiento</label>
            <input type="text" class="form-control" id="esta_rent" name="esta_rent" required>
        </div>
        <div class="mb-3">
            <label for="patent_rent" class="form-label">Patente</label>
            <input type="text" class="form-control" id="patent_rent" name="patent_rent" required>
        </div>

        <button type="submit" class="btn btn-primary">Enviar</button>

		<?php 

				wp_nonce_field('insert_owner', 'owner_nonce'); 
		?>

			</form>
		</div>

    <?php

    return ob_get_clean(); 

	}
}
add_shortcode('arriendo-dia', 'render_insert_arriendo');

#########################################################################
#########################  BUSCAR ARRIENDO POR RUT ######################

function buscar_rent_callback() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cm_rents';

    $rut = sanitize_text_field($_POST['rut']);

    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE rut_rent = %s", $rut);
    $results = $wpdb->get_results($query);

    if (count($results) > 0) {

        echo '<table class="table">';
        echo '<thead><tr><th>Nombre</th><th>Apellido</th><th>Rut</th><th>Entrada</th><th>Salida</th></tr></thead>';
        echo '<tbody>';

        foreach ($results as $row) {
            echo '<tr>';
            echo '<td>' . esc_html($row->nombre_rent) . '</td>';
            echo '<td>' . esc_html($row->apellido_rent) . '</td>';
            echo '<td>' . esc_html($row->rut_rent) . '</td>';
            echo '<td>' . esc_html($row->dia_ini) . '</td>';
            echo '<td>' . esc_html($row->dia_fin) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';

		echo '<table class="table">';
        echo '<thead><tr><th>Telf</th><th>Torre</th><th>Depa</th><th>Parking</th><th>Bodega</th><th>Email</th></tr></thead>';
        echo '<tbody>';

        foreach ($results as $row) {
            echo '<tr>';

            echo '<td>' . esc_html($row->torre_rent) . '</td>';
            echo '<td>' . esc_html($row->depa_rent) . '</td>';
            echo '<td>' . esc_html($row->esta_rent) . '</td>';
            echo '<td>' . esc_html($row->patent_rent) . '</td>';
			echo '<td>' . esc_html($row->telf_rent) . '</td>'; 
			echo '<td>' . esc_html($row->email_rent) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {

        echo 'Rut no encontrado xxx.';
    }

    wp_die();
}

add_action('wp_ajax_buscar_rent', 'buscar_rent_callback');
add_action('wp_ajax_nopriv_buscar_rent', 'buscar_rent_callback');

#########################################################################
############## MOSTRAR TODOS LOS ARRIENDOS POR DIA ######################

function mostrar_rent4day_shortcode($atts) {

	if(limit_the_user('guardias')===true ||  limit_the_user('gerente')===true || limit_the_user('administrator')===true)
	{

    $registros_por_pagina = 10;

    $pagina = (isset($_POST['pagina'])) ? $_POST['pagina'] : 1;

    global $wpdb;
    $table_name = $wpdb->prefix . 'cm_rents';

    $offset = ($pagina - 1) * $registros_por_pagina;

    $query = $wpdb->prepare("SELECT * FROM $table_name LIMIT %d, %d", $offset, $registros_por_pagina);
    $results = $wpdb->get_results($query);

    $output = '<div class="table-responsive"><table class="table table-striped " id="rent4day-table" >';
    $output .= '<thead><tr><th>Fechas</th><th>Nombre</th><th>Apellido</th><th>RUT</th><th>Telefono</th><th>Email</th><th>Torre</th><th>Depa</th><th>Parking</th><th>Patente</th></tr></thead>';
    $output .= '<tbody>';

    foreach ($results as $row) {
        $output .= '<tr>';
        $output .= '<td>' . $row->dia_ini .'/'.$row->dia_fin. '</td>';
        $output .= '<td>' . $row->nombre_rent . '</td>';
        $output .= '<td>' . $row->apellido_rent . '</td>';
        $output .= '<td>' . $row->rut_rent . '</td>';
        $output .= '<td>' . $row->telf_rent . '</td>';
        $output .= '<td>' . $row->email_rent . '</td>';
        $output .= '<td>' . $row->torre_rent . '</td>';
        $output .= '<td>' . $row->depa_rent . '</td>';
        $output .= '<td>' . $row->esta_rent . '</td>';
        $output .= '<td>' . $row->patent_rent . '</td>';
        $output .= '</tr>';
    }

    $output .= '</tbody>';
    $output .= '</table></div>';

    $total_registros = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");
    $total_paginas = ceil($total_registros / $registros_por_pagina);

    $output .= '<div class="pagination">';
    for ($i = 1; $i <= $total_paginas; $i++) {
        $output .= '<a class="page-link" href="#" data-pagina="' . $i . '">' . $i . '</a>';
    }
    $output .= '</div>';

    return $output;

	}
}

add_shortcode('mostrar_rent4day', 'mostrar_rent4day_shortcode');

#####################################################################
#################FUNCION AJAX PARA CARGAR POR PAGINA LOS ARRIENDOS

function load_rent4day_callback() {

    $pagina = (isset($_POST['pagina'])) ? $_POST['pagina'] : 1;

    $registros_por_pagina = 10;
    $offset = ($pagina - 1) * $registros_por_pagina;

    global $wpdb;
    $table_name = $wpdb->prefix . 'cm_rents';

    $query = $wpdb->prepare("SELECT * FROM $table_name LIMIT %d, %d", $offset, $registros_por_pagina);
    $results = $wpdb->get_results($query);

    $output = '<table class="table table-striped"  >';
    $output .= '<thead><tr><th>ID</th><th>Nombre</th><th>Apellido</th><th>RUT</th><th>Telefono</th><th>Email</th><th>Torre</th><th>Depa</th><th>Parking</th><th>Bodega</th></tr></thead>';
    $output .= '<tbody>';

    foreach ($results as $row) {
        $output .= '<tr>';
        $output .= '<td>' . $row->id . '</td>';
        $output .= '<td>' . $row->nombre_rent . '</td>';
        $output .= '<td>' . $row->apellido_rent . '</td>';
        $output .= '<td>' . $row->rut_rent . '</td>';
        $output .= '<td>' . $row->telf_rent . '</td>';
        $output .= '<td>' . $row->email_rent . '</td>';
        $output .= '<td>' . $row->torre_rent . '</td>';
        $output .= '<td>' . $row->depa_rent . '</td>';
        $output .= '<td>' . $row->esta_rent . '</td>';
        $output .= '<td>' . $row->patent_rent . '</td>';
        $output .= '</tr>';
    }

    $output .= '</tbody>';
    $output .= '</table>';

    echo $output;

    wp_die();
}

add_action('wp_ajax_load_rent4day', 'load_rent4day_callback');
add_action('wp_ajax_nopriv_load_rent4day', 'load_rent4day_callback');

###############################################################################
###############################################################################

function cm_visit_shortcode() {

   if(limit_the_user('guardias')===true ||  limit_the_user('gerente')===true || limit_the_user('administrator')===true)
	{

    $registros_por_pagina = 10;

    $pagina = (isset($_POST['pagina'])) ? $_POST['pagina'] : 1;

    global $wpdb;
    $table_name = $wpdb->prefix . 'cm_visit';

    $offset = ($pagina - 1) * $registros_por_pagina;

    $query = $wpdb->prepare("SELECT * FROM $table_name LIMIT %d, %d", $offset, $registros_por_pagina);
    $results = $wpdb->get_results($query);

    $output = '<div class="table-responsive"><table class="table table-striped " id="visit-table" >';
    $output .= '<thead><tr><th>nombre</th><th>apellido</th><th>rut</th><th>patente</th><th>torre</th><th>apart</th><th>habilita</th><th>fecha</th><th>hora</th><th>id</th></tr></thead>';
    $output .= '<tbody>';

    foreach ($results as $row) {
        $output .= '<tr>';
		$output .= '<td>' . $row->nombre . '</td>';
		$output .= '<td>' . $row->apellido . '</td>';
		$output .= '<td>' . $row->rut . '</td>';
		$output .= '<td>' . $row->patente . '</td>';
		$output .= '<td>' . $row->torre . '</td>';
		$output .= '<td>' . $row->apart . '</td>';
		$output .= '<td>' . $row->habilita . '</td>';
		$output .= '<td>' . $row->fecha . '</td>';
		$output .= '<td>' . $row->hora . '</td>';
		$output .= '<td>' . $row->id . '</td>';
		$output .= '</tr>';
    }

    $output .= '</tbody>';
    $output .= '</table></div>';

    $total_registros = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");
    $total_paginas = ceil($total_registros / $registros_por_pagina);

    $output .= '<div class="pagination">';
    for ($i = 1; $i <= $total_paginas; $i++) {
        $output .= '<a class="page-link" href="#" data-pagina="' . $i . '">' . $i . '</a>';
    }
    $output .= '</div>';

    return $output;

	}

}
add_shortcode('cm_visit', 'cm_visit_shortcode');

###########################################################################
function load_visit_callback() {

    $pagina = (isset($_POST['pagina'])) ? $_POST['pagina'] : 1;

    $registros_por_pagina = 10;
    $offset = ($pagina - 1) * $registros_por_pagina;

    global $wpdb;
    $table_name = $wpdb->prefix . 'cm_visit';

    $query = $wpdb->prepare("SELECT * FROM $table_name LIMIT %d, %d", $offset, $registros_por_pagina);
    $results = $wpdb->get_results($query);

    $output = '<table class="table table-striped"  >';
    $output .= '<thead><tr><th>nombre</th><th>apellido</th><th>rut</th><th>patente</th><th>torre</th><th>apart</th><th>habilita</th><th>fecha</th><th>hora</th><th>id</th></tr></thead>';
    $output .= '<tbody>';

    foreach ($results as $row) {
        $output .= '<tr>';
		$output .= '<td>' . $row->nombre . '</td>';
		$output .= '<td>' . $row->apellido . '</td>';
		$output .= '<td>' . $row->rut . '</td>';
		$output .= '<td>' . $row->patente . '</td>';
		$output .= '<td>' . $row->torre . '</td>';
		$output .= '<td>' . $row->apart . '</td>';
		$output .= '<td>' . $row->habilita . '</td>';
		$output .= '<td>' . $row->fecha . '</td>';
		$output .= '<td>' . $row->hora . '</td>';
		$output .= '<td>' . $row->id . '</td>';
		$output .= '</tr>';
    }

    $output .= '</tbody>';
    $output .= '</table>';

    echo $output;

    wp_die();
}

add_action('wp_ajax_load_visit', 'load_visit_callback');
add_action('wp_ajax_nopriv_load_visit', 'load_visit_callback');

##################################################################

function render_visit_form() {

	if(limit_the_user('guardias')===true || limit_the_user('administrator')===true)
	{

    ob_start(); 

    ?>
   <div class="container"> <div class="row justify-content-center align-items-center" >

        <form action="procesar_formulario.php" id="visit-form" method="POST" class="col-12 col-md-6">
		<h2>Formulario de Visitas</h2>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="apellido" class="form-label">Apellido:</label>
                <input type="text" class="form-control" id="apellido" name="apellido" required>
            </div>
            <div class="mb-3">
                <label for="rut" class="form-label">RUT:</label>
				<input type="text" id="rut" name="rut" class="rut-input form-control" placeholder="Ejemplo: 12345678-9" required>

				<div id="rut-message" class="rut-message"></div>
				<div id="rut-error" class="rut-error"></div>	
            </div>
            <div class="mb-3">
                <label for="patente" class="form-label">patente:</label>
                <input type="text" class="form-control" id="patente" name="patente" required>
            </div>

			<div class="mb-3">
				<label for="torre" class="form-label">Torre:</label>
				<select class="form-control" id="torre" name="torre" required>
					<option value="">Seleccionar Torre</option>
					<option value="Toconao Sur 1">Toconao Sur 1</option>
					<option value="Talabre Sur">Talabre Sur</option>
					<option value="Talabre Norte 2">Talabre Norte 2</option>
					<option value="Toconao Norte">Toconao Norte</option>
					<option value="Atacama 1">Atacama 1</option>
					<option value="Talabre Norte 1">Talabre Norte 1</option>
					<option value="Atacama 2">Atacama 2</option>
					<option value="Toconao Sur 2">Toconao Sur 2</option>
				</select>
			</div>

			<div class="mb-3">
                <label for="apart" class="form-label">Apartamento:</label>
                <input type="text" class="form-control" id="apart" name="apart" required>
            </div>

            <div class="mb-3">
                <label for="habilita" class="form-label">Habilita:</label>
                <input type="text" class="form-control" id="habilita" name="habilita" required>
            </div>
            <div class="mb-3">
                <label for="f1" class="form-label">Fecha:</label>
                <input type="date" class="form-control" id="f1" name="f1" required>
            </div>
            <div class="mb-3">
                <label for="f2" class="form-label">Hora:</label>
                <input type="time" class="form-control" id="f2" name="f2" required>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>

			<?php 

				wp_nonce_field('insert_owner', 'owner_nonce'); 
			?>
        </form>
    </div></div>
    <?php

    return ob_get_clean(); 

	}
}
add_shortcode('visit-form', 'render_visit_form');

add_action('wp_ajax_visit_action', 'visit_action');
add_action('wp_ajax_nopriv_visit_action', 'visit_action');
function visit_action() {

	if ( !isset($_POST['owner_nonce']) || ! wp_verify_nonce( $_POST['owner_nonce'], 'insert_owner' ) ) {
         die ( 'Acceso no autorizado! '.$_POST['owner_nonce']);
    }

		$nombre= sanitize_text_field($_POST['nombre']);
		$apellido = sanitize_text_field($_POST['apellido']);
		$rut= sanitize_text_field($_POST['rut']);
		$patente = sanitize_text_field($_POST['patente']);
		$habilita = sanitize_text_field($_POST['habilita']);
		$apart = sanitize_text_field($_POST['apart']);
		$torre = sanitize_text_field($_POST['torre']);
		$fecha = sanitize_text_field($_POST['f1']);
		$hora = sanitize_text_field($_POST['f2']);

    global $wpdb;
    $table_name = $wpdb->prefix . 'cm_visit';
		$result=
		$wpdb->insert(
        $table_name,
        array(
            'nombre'   => $nombre,
            'apellido' => $apellido,
            'rut'      => $rut,
            'patente'     => $patente,
            'torre'    => $torre,
            'apart'     => $apart,
            'habilita'    => $habilita,
            'fecha'  => $fecha,
			'hora'   => $hora
		),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')

    );
	;

	if($result === false)
	{
		$response=0;
	}else
	{
		$response=1;
	}

    echo $response;

    wp_die();
}

##################################################################

function render_mantencion_form() {

	if(limit_the_user('administrator')===true ||  limit_the_user('gerente')===true)
	{

	ob_start(); 

    ?>
	<div class="container"> <div class="row justify-content-center align-items-center" >

	<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="col-12 col-md-6">
	<div class="mb-3">
    <input type="hidden" name="action" value="insert_equipo">
    <label for="equipo_nombre" class="form-label">Nombre del Equipo:</label>
    <input type="text"  class="form-control" name="equipo_nombre" required>
	</div>
	<div class="mb-3"> 
    <label for="cantidad_1" class="form-label" >Total existente:</label>
    <input type="text" name="cantidad_1" class="form-control" required>
	</div>
	<div class="mb-3">
    <label for="cantidad_2" class="form-label" >Dañado/Quemado/Faltante:</label>
    <input type="text" name="cantidad_2" class="form-control" required>
	</div>
    <input type="submit" class="btn btn-primary" value="Agregar Equipo">
</form> </div></div>
  <?php

    return ob_get_clean(); 

	}
}
add_shortcode('mantencion-form', 'render_mantencion_form');

function insert_equipo() {

	if(limit_the_user('administrator')===true ||  limit_the_user('gerente')===true)
	{

    if (isset($_POST['equipo_nombre']) && isset($_POST['cantidad_1']) && isset($_POST['cantidad_2'])) {
        $equipo_nombre = sanitize_text_field($_POST['equipo_nombre']);
        $cantidad_1 = sanitize_text_field($_POST['cantidad_1']);
        $cantidad_2 = sanitize_text_field($_POST['cantidad_2']);

        $post_args = array(
            'post_title'    => $equipo_nombre,
            'post_type'     => 'equipo', 
            'post_status'   => 'publish',
        );

        $post_id = wp_insert_post($post_args);

        update_post_meta($post_id, '_cantidad_1', $cantidad_1);

        update_post_meta($post_id, '_cantidad_2', $cantidad_2);

        wp_redirect(home_url('/agregar-item-mantencion')); 

        exit;
    }

	}
}

add_action('admin_post_insert_equipo', 'insert_equipo');
add_action('admin_post_nopriv_insert_equipo', 'insert_equipo');

?>