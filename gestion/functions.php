<?php
add_action( 'after_setup_theme', 'support_theme_function' );

function support_theme_function() {

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'custom-header' );
		add_theme_support( 'menu' );
		add_theme_support('custom-logo', array(
        'height'      => 100, 
        'width'       => 150, 
        'flex-height' => true,
        'flex-width'  => true,
    ));
}

function obtener_url_imagen_destacada() {

    $imagen_destacada_id = get_post_thumbnail_id();

    if ($imagen_destacada_id) {

        $imagen_destacada_url = wp_get_attachment_url($imagen_destacada_id);
    } else {

        $imagen_destacada_url = get_template_directory_uri() . '/assets/images/hero_bg_3.jpg';
    }

    return $imagen_destacada_url;
}

add_action( 'after_setup_theme', 'custom_class_walker_nav_menu' );

	function custom_class_walker_nav_menu() {

		require_once 'custom-class-walker-nav-menu.php';

	}

register_nav_menus( 

	array(
		'menu-top' => 'Main menu Top',
		'top_mobile' => 'Mobile Navigation Menu',
		'footer_menu' => 'Footer Menu',	
		'top-guardias' => 'Menu Top Guardias',
		'no-login' => 'Menu Visitas',
	) 
);

function registrar_custom_post_type() {
    $labels = array(
        'name'               => 'Equipos', 
        'singular_name'      => 'Equipo',
        'menu_name'          => 'Equipos',
        'name_admin_bar'     => 'Equipo',
        'add_new'            => 'Agregar Nuevo',
        'add_new_item'       => 'Agregar Nuevo Equipo',
        'new_item'           => 'Nuevo Equipo',
        'edit_item'          => 'Editar Equipo',
        'view_item'          => 'Ver Equipo',
        'all_items'          => 'Todos los Equipos',
        'search_items'       => 'Buscar Equipos',
        'parent_item_colon'  => 'Equipo Padre:',
        'not_found'          => 'No se encontraron Equipos.',
        'not_found_in_trash' => 'No se encontraron Equipos en la papelera.'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'equipo' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'custom-fields' )
    );

    register_post_type( 'equipo', $args );
}

add_action( 'init', 'registrar_custom_post_type' );

function agregar_campos_personalizados() {
    add_meta_box( 'cantidad_meta_box', 'Cantidades', 'mostrar_campos_cantidad', 'equipo', 'normal', 'high' );
}

add_action( 'add_meta_boxes', 'agregar_campos_personalizados' );

function mostrar_campos_cantidad( $post ) {
    wp_nonce_field( 'guardar_cantidad_meta_box', 'cantidad_meta_box_nonce' );

    $cantidad_1 = get_post_meta( $post->ID, '_cantidad_1', true );
    $cantidad_2 = get_post_meta( $post->ID, '_cantidad_2', true );

    echo '<label for="cantidad_1">Cantidad 1:</label>';
    echo '<input type="text" id="cantidad_1" name="cantidad_1" value="' . esc_attr( $cantidad_1 ) . '" />';

    echo '<br>';

    echo '<label for="cantidad_2">Cantidad 2:</label>';
    echo '<input type="text" id="cantidad_2" name="cantidad_2" value="' . esc_attr( $cantidad_2 ) . '" />';
}

function guardar_campos_cantidad( $post_id ) {
    if ( ! isset( $_POST['cantidad_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['cantidad_meta_box_nonce'], 'guardar_cantidad_meta_box' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( isset( $_POST['cantidad_1'] ) ) {
        update_post_meta( $post_id, '_cantidad_1', sanitize_text_field( $_POST['cantidad_1'] ) );
    }

    if ( isset( $_POST['cantidad_2'] ) ) {
        update_post_meta( $post_id, '_cantidad_2', sanitize_text_field( $_POST['cantidad_2'] ) );
    }
}

add_action( 'save_post', 'guardar_campos_cantidad' );

function charts_equipos($d1, $d2, $d3, $d4)
{
	$code='
	<div class="canvas_size">
	  <canvas id="myChart_'.$d4.'"></canvas>
	</div>
	';
	$code.="<script>
	const ctx_$d4 = document.getElementById('myChart_$d4');

	  new Chart(ctx_$d4, {
		type: 'bar',
		data: {
		  labels: ['Total', 'Faltantes'],
		  datasets: [{
			label: '$d1',
			data: [$d2, $d3],
			borderWidth: 1,
			borderColor: ['#36A2EB','#FF6262'],
		    backgroundColor: ['#9BD0F5','#FF7C30']
		  }]
		},

		options: {
		indexAxis: 'y',

		elements: {
		  bar: {
			borderWidth: 2,
		  }
		},
		responsive: true,
		plugins: {
		  legend: {
			position: 'right',
		  },
		  title: {
			display: true,
			text: '$d1'
		  }
		}
	  },
	  });
	</script>
	";

	return ($code);
}

function obtener_datos_custom_post_types() {

    $args = array(
        'post_type'      => 'equipo',
        'posts_per_page' => -1, 
		'order' => 'ASC',
		'orderby' => 'ID',
    );

    $equipos = get_posts($args);

    $output = '<div class="row">';

    foreach ($equipos as $equipo) {
        $post_id = $equipo->ID;

        $titulo      = get_the_title($post_id);
        $cantidad_1  = get_post_meta($post_id, '_cantidad_1', true);
        $cantidad_2  = get_post_meta($post_id, '_cantidad_2', true);
		$code = charts_equipos($titulo, $cantidad_1, $cantidad_2, $post_id);

		$output .= '<div class="col-12 col-md-6 col-lg-4 aos-init aos-animate" data-aos="fade-up" data-aos-delay="300">';

        $output .= "<div class='equipo box-feature mb-12'>";
		$output .= "<h2>$titulo</h2>";
		$output .= $code;
        $output .= "<p>Total: $cantidad_1</p>";
        $output .= "<p>Faltantes y/o Quemadas: $cantidad_2</p>";		
		$output .= "</div>";

		$output .='</div>';
    }
	$output .='</div>';
    return $output;
}

function custom_post_type_shortcode() {
	if(limit_the_user('administrator')===true ||  limit_the_user('gerente')===true)
	{
		return obtener_datos_custom_post_types(
		);
	}
}

add_shortcode('mostrar_equipos', 'custom_post_type_shortcode');

function agregar_rol_empleados() {

    $rol = get_role('guardias');
	$rol_ = get_role('gerente');

    if (!$rol) {

        add_role(
            'guardias',
            __('Guardias'),
            array(
                'read' => true, 
            )
        );
    }
	if (!$rol_) {

        add_role(
            'gerente',
            __('Admin Condo'),
            array(
                'read' => true, 
            )
        );
    }
}

add_action('after_switch_theme', 'agregar_rol_empleados');

function limit_the_user($u)
{
			$user = wp_get_current_user();
            $roles = $user->roles;

			if (in_array($u, $roles) ) {				
				return true;
			}
			else
			{
				return false;				 
			}
}

function crear_custom_post_type() {
    register_post_type('ticket',
        array(
            'labels' => array(
                'name' => __('Tickets'),
                'singular_name' => __('Ticket')
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'custom-fields'),
        )
    );
}

add_action('init', 'crear_custom_post_type');

function registrar_taxonomia_problema() {
    register_taxonomy(
        'problemas',
        'ticket',
        array(
            'label' => __('Problemas'),
            'hierarchical' => true,
        )
    );
}

add_action('init', 'registrar_taxonomia_problema');

function agregar_metaboxes() {
	add_meta_box( 'valores_meta_box', 'Información Extra', 'contenido_metabox1', 'ticket', 'normal', 'high' );
}

add_action('add_meta_boxes', 'agregar_metaboxes');

function contenido_metabox1($post) {
    wp_nonce_field( 'guardar_cantidad_meta_box', 'cantidad_meta_box_nonce' );

    $valor_1 = get_post_meta( $post->ID, '_valor_1', true );
    $valor_2 = get_post_meta( $post->ID, '_valor_2', true );

    echo '<label for="valor_1">Fecha problema:</label>';
    echo '<input type="text" id="valor_1" name="valor_1" value="' . esc_attr( $valor_1 ) . '" />';

    echo '<br>';

    echo '<label for="valor_2">Cantidad 2:</label>';
    echo '<input type="text" id="valor_2" name="valor_2" value="' . esc_attr( $valor_2 ) . '" />';
}

function guardar_campos_cantidad_tickes( $post_id ) {
    if ( ! isset( $_POST['cantidad_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['cantidad_meta_box_nonce'], 'guardar_cantidad_meta_box' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( isset( $_POST['valor_1'] ) ) {
        update_post_meta( $post_id, '_valor_1', sanitize_text_field( $_POST['valor_1'] ) );
    }

    if ( isset( $_POST['valor_2'] ) ) {
        update_post_meta( $post_id, '_valor_2', sanitize_text_field( $_POST['valor_2'] ) );
    }
}

add_action( 'save_post', 'guardar_campos_cantidad_tickes' );

########### LIBRO DIGITAL
########### LIBRO DIGITAL

function render_problemas_form() {

	if(limit_the_user('administrator')===true ||  limit_the_user('gerente')===true)
	{

	$select_o="<option value=''>Seleccione un tema</option>";

	$terms = get_terms(array(
		'taxonomy' => 'problemas',
		'hide_empty' => false, 
	));

	if ($terms && !is_wp_error($terms)) {	

		foreach ($terms as $term) {			

			if ($term->parent != 0) {
              $select_o.= "<option value='$term->parent,$term->term_id'> $term->name </option>";
            }

		}
	} else {
		echo 'No se encontraron términos para esta taxonomía.';
	}

	ob_start(); 

    ?>
	<div class="container"> <div class="row justify-content-center align-items-center" >
	<form method="post" action="" id="problem-form" class="col-12 col-md-6">
	<h2>Crear Tiket</h2>
    <div class="mb-3">
	<label for="problema" class="form-label">Seleccione un tema:</label>
    <select name="problema" required class="form-control">
        <?php        
			echo  $select_o;
        ?>
    </select>
	</div>
	<div class="mb-3">
    <!-- Otros campos del formulario -->
    <label for="titulo" class="form-label">Título:</label>
    <input type="text" name="titulo" class="form-control" required>
	</div>
	<div class="mb-3">
    <!-- Otros campos del formulario -->
    <label for="fecha_o" class="form-label">Fecha de origen:</label>
    <input type="date" name="fecha_o" class="form-control" required>
	</div>
	<div class="mb-3">
    <!-- Otros campos del formulario -->
    <label for="titulo" class="form-label">Descripción:</label>
    <textarea  name="descripcion" class="form-control" required maxlength="150"></textarea>
	</div>
    <!-- Agrega más campos según tus necesidades -->
	<div class="mb-3">
    <input type="submit" class="btn btn-primary" value="Crear Ticket">
	</div>
	<?php 

		wp_nonce_field('insert_owner', 'owner_nonce'); 
	?>
	</form>
	</div></div>

	  <?php

    return ob_get_clean(); 
	}
}
add_shortcode('problemas-form', 'render_problemas_form');

add_action('wp_ajax_problem_action', 'problem_action');
add_action('wp_ajax_nopriv_problem_action', 'problem_action');
function problem_action() {

	$response="0";

	if ( !isset($_POST['owner_nonce']) || ! wp_verify_nonce( $_POST['owner_nonce'], 'insert_owner' ) ) {
         die ( 'Acceso no autorizado! '.$_POST['owner_nonce']);
    }

    $titulo = sanitize_text_field($_POST['titulo']);
    $descripcion = sanitize_text_field($_POST['descripcion']);
    $categorias = $_POST['problema']; 
    $valor_1 = sanitize_text_field($_POST['fecha_o']);
    $valor_2 = sanitize_text_field($_POST['valor_2']);

	$categorias=explode(",", $categorias);

	$term = get_term( $categorias[0] );

    $nuevo_post = array(
        'post_title'    => $titulo,
        'post_content'  => $descripcion,
        'post_status'   => 'publish',

        'post_type'     => 'ticket',
		'tax_input' => array(
                'problemas' =>  $categorias

            )
    );

    $post_id = wp_insert_post($nuevo_post);

    update_post_meta($post_id, '_valor_1', $valor_1);
    update_post_meta($post_id, '_valor_2', $term->name);

     if ($post_id) {
        $response= 'Ticket creado exitosamente ';
    } else {
        $response= 'Error';
    }

    echo $response;

    wp_die();
}

#######################################################3
###MOATRar tikets
########################################################

function custom_ticket_table_shortcode() {

    $args = array(
        'post_type'      => 'ticket',
        'posts_per_page' => -1, 
		'order' => 'ASC',
		'orderby' => 'ID',
    );

    $equipos = get_posts($args);

    $output = '<div class="row">';

    foreach ($equipos as $equipo) {
        $post_id = $equipo->ID;

        $titulo      = get_the_title($post_id);
        $cantidad_1  = get_post_meta($post_id, '_valor_1', true);
        $cantidad_2  = get_post_meta($post_id, '_valor_2', true);

		$output .= '<div class="col-12 col-md-6 col-lg-4 aos-init aos-animate" data-aos="fade-up" data-aos-delay="300">';

        $output .= "<div class='equipo box-feature mb-12'>";
		$output .= "<h2>$titulo</h2>";

        $output .= "<p>Total: $cantidad_1</p>";
        $output .= "<p>Faltantes y/o Quemadas: $cantidad_2</p>";		
		$output .= "</div>";

		$output .='</div>';
    }
	$output .='</div>';
    return $output;
}

add_shortcode('ver_registros_libro', 'custom_ticket_table_shortcode');

function custom_ticket_table_ajax_handler() {
    $page = $_POST['page'];

    $args = array(
        'post_type' => 'ticket',
        'posts_per_page' => 1,
        'paged' => $page
    );

    $tickets = get_posts($args);

    if ($tickets) {
        foreach ($tickets as $ticket) {
            $title = get_the_title($ticket->ID);
            $description = get_the_excerpt($ticket->ID);

            echo "<tr><td>{$title}</td><td>{$description}</td></tr>";
        }
    }

    die();
}

add_action('wp_ajax_custom_ticket_table', 'custom_ticket_table_ajax_handler');
add_action('wp_ajax_nopriv_custom_ticket_table', 'custom_ticket_table_ajax_handler');

######################################mostrar tikets 2

function portfolios_shortcode($atts){
	extract( shortcode_atts( array(
		'expand' => '',
	), $atts) );

    global $paged;
    $posts_per_page = 10;
    $settings = array(
        'showposts' => $posts_per_page, 
        'post_type' => 'ticket', 
        'orderby' => 'date', 
        'order' => 'DESC', 
        'paged' => $paged
    );

    $post_query = new WP_Query( $settings );	
    $childn="";

    $total_found_posts = $post_query->found_posts;
    $total_page = ceil($total_found_posts / $posts_per_page);

	$list = '';

	$list.='
	<div class="table-responsive">
	<table class="table table-striped " id="visit-table">
	<thead>
	<tr>
		<th>Categoría</th>	<th>Título</th> <th>Comentario</th> <th>Fecha Incidente</th>  <th>Fecha Creación</th>
	</tr>
	</thead>
	<tbody>
	';

	while($post_query->have_posts()) : $post_query->the_post();

		$terms = wp_get_post_terms( get_the_ID(), 'problemas',  array( 'fields' => 'all' ) );
		$fecha  = get_post_meta(get_the_ID(), '_valor_1', true);
		$clase  = get_post_meta(get_the_ID(), '_valor_2', true);
		$terms = wp_get_post_terms( get_the_ID(), 'problemas',  array( 'fields' => 'all' ) );
		if ($terms && !is_wp_error($terms)) {	

			foreach ($terms as $term) {				
					if ($term->parent != 0) {
					  $childn=$term->name;
					}				
			}
		}
		$clase=str_replace(" ","_",$clase);
		$title=get_the_title();
		$list.= '
		<tr class="'.$clase.'">
		<td><b>'.$childn.'</b></td> <td>'.$title.'</td><td>'.get_the_content().'</td> <td>'.$fecha.'</td><td>'.get_the_date('d/m/YS') .'  '.get_the_time('g:i a').' </td>
		</tr>
		';

		$list .= '';        
	endwhile;

	$list.= '
	</tbody></table></div>
	';

    if(function_exists('wp_pagenavi')) {
        $list .='<div class="page-navigation">'.wp_pagenavi(array('query' => $post_query, 'echo' => false)).'</div>';
    } else {
        $list.='<div id="testimonial-nav">
        <span class="next-posts-links next" data-controls="next">'.get_next_posts_link('Next page', $total_page).'</span>
        <span class="prev-posts-links prev" data-controls="prev">'.get_previous_posts_link('Previous page').'</span>
		</div>
        ';
    }

	return $list;
}
add_shortcode('portfolios', 'portfolios_shortcode');