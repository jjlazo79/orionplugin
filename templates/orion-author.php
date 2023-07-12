<?php get_header();

global $wpdb;

$curauth            = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
$wpamelia_provider  = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}amelia_users WHERE type = 'provider' AND externalId = $curauth->ID", OBJECT);
$tipocoach_referrer = (isset($_GET["tipocoaching"])) ? '&tipocoaching=' . $_GET["tipocoaching"] : '';
$precio_referrer    = (isset($_GET["precio"])) ? '&precio=' . $_GET["precio"] : '';
$tipocoach          = wp_get_object_terms($curauth->ID, 'tipocoach');
$long_description   = get_user_meta($curauth->ID, 'long_description', true);
$user_certification = get_user_meta($curauth->ID, 'icf_certification', true);
$video              = get_user_meta($curauth->ID, 'user_video', true);
$long_description   = htmlspecialchars_decode($long_description);
$long_description   = wpautop($long_description);
$register_link      = '<a href="' . wp_registration_url() . '">Regístrate</a>'; //'<a href="/registro/">Regístrate</a>'; 

$services_asigned = OrionFunctions::getUserservicesByUserId($curauth->ID);
?>

<section id="content" class="elementor-section elementor-section-boxed" style="background-color: var(--e-global-color-abc8a69 );">
    <div class="elementor-container elementor-column-gap-default">
        <div class="wp-block-group is-layout-constrained">
            <div class="wp-block-group__inner-container">
                <div class="wp-block-columns is-layout-flex wp-container-4">

                    <div class="wp-block-column is-layout-flow">
                        <div class="wp-block-image">
                            <figure class="aligncenter size-medium">
                                <img decoding="async" src="<?php echo $wpamelia_provider[0]->pictureFullPath; ?>">
                            </figure>
                        </div>
                    </div>
                    <div class="wp-block-column is-layout-flow">
                        <h2 class="wp-block-heading has-text-align-center"><?php echo $wpamelia_provider[0]->firstName . ' ' . $wpamelia_provider[0]->lastName; ?></h2>
                        <p class="has-text-align-center"><?php echo $wpamelia_provider[0]->description; ?></p>



                        <h3>Objetivos</h3>
                        <?php if (!empty($tipocoach) && !is_wp_error($tipocoach)) : ?>
                            <ul>
                                <?php foreach ($tipocoach as $term) : ?>
                                    <li><a target="_blank" href="https://orioncoaching.es/coaches/?objetivo=<?php echo $term->slug; ?>"><?php echo esc_html($term->name); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <hr>

                        <h3>Precios</h3>
                        <table class="table_price">
                            <thead>
                                <tr>
                                    <th>Proceso</th>
                                    <th>Descripción</th>
                                    <th>Precio</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($services_asigned as $service_obj) : ?>
                                    <?php $price = (is_user_logged_in()) ? $service_obj->price . ' €' : $register_link;  ?>
                                    <tr>
                                        <td><?php echo $service_obj->name; ?></td>
                                        <td><?php echo $service_obj->description; ?></td>
                                        <td><?php echo $price; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>



                        <div class="wp-block-buttons is-content-justification-center is-layout-flex wp-container-2">
                            <div class="wp-block-button">
                                <a class="wp-block-button__link has-text-align-center wp-element-button" href="/pedir-cita-coach/?coach=<?php echo $wpamelia_provider[0]->firstName  . ' ' . $wpamelia_provider[0]->lastName; ?><?php echo $tipocoach_referrer; ?><?php echo $precio_referrer; ?>">
                                    Comienza tu coaching conmigo
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<section id="content" class="elementor-section elementor-section-boxed">
    <div class="elementor-container elementor-column-gap-default">
        <div class="wp-block-group is-layout-constrained">
            <div class="wp-block-group__inner-container">
                <div class="wp-block-columns is-layout-flex wp-container-1">
                    <div class="wp-block-column is-layout-flow">
                        <div class="tabs">
                            <input type="radio" id="tab1" name="tab" checked>
                            <label for="tab1"><i class="fa fa-user"></i>
                                <span>Sobre mi</span>
                            </label>
                            <input type="radio" id="tab2" name="tab">
                            <label for="tab2"><i class="fa fa-certificate"></i>
                                <span>Certificación ICF</span>
                            </label>
                            <input type="radio" id="tab3" name="tab">
                            <label for="tab3"><i class="fa fa-pencil"></i>
                                <span>Opiniones</span>
                            </label>
                            <input type="radio" id="tab4" name="tab">
                            <label for="tab4"><i class="fa fa-video-camera"></i>
                                <span>Vídeo</span>
                            </label>
                            <div class="line"></div>
                            <div class="content-container">
                                <div class="content" id="c1">
                                    <h3>Sobre mi</h3>
                                    <?php echo $long_description; ?>
                                </div>
                                <div class="content" id="c2">
                                    <h3>Certificación ICF</h3>
                                    <?php echo $user_certification; ?>
                                </div>
                                <div class="content" id="c3">
                                    <h3>Opiniones</h3>
                                    <?php echo do_shortcode('reviews'); ?>
                                </div>
                                <div class="content" id="c4">
                                    <h3>Vídeo</h3>
                                    <?php if ($video) {
                                        echo $video;
                                    } else {
                                        echo '<p>' . _e('No hay vídeo disponible', ORION_TEXT_DOMAIN) . '</p>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .table_price {
        margin-bottom: 30px;
    }

    /* TABS */
    .tabs {
        /* width: 400px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%); */
        font-size: 0;
        box-shadow: 0 0 100px RGBa(0, 0, 0, 0.5);
        border-radius: 3px;
        overflow: hidden;
        width: 1140px;
        margin: 0 auto;
        max-width: 1140px;
    }

    .tabs input {
        display: none;
    }

    .tabs input:checked+label {
        background: #eee;
    }

    .tabs input#tab1:checked~.line {
        left: 0%;
    }

    .tabs input#tab1:checked~.content-container #c1 {
        opacity: 1;
        visibility: visible;
        transition: 0.25s ease;
    }

    .tabs input#tab2:checked~.line {
        left: 25%;
    }

    .tabs input#tab2:checked~.content-container #c2 {
        opacity: 1;
        visibility: visible;
        transition: 0.25s ease;
    }

    .tabs input#tab3:checked~.line {
        left: 50%;
    }

    .tabs input#tab3:checked~.content-container #c3 {
        opacity: 1;
        visibility: visible;
        transition: 0.25s ease;
    }

    .tabs input#tab4:checked~.line {
        left: 75%;
    }

    .tabs input#tab4:checked~.content-container #c4 {
        opacity: 1;
        visibility: visible;
        transition: 0.25s ease;
    }

    .tabs label {
        display: inline-block;
        font-size: 16px;
        height: 36px;
        line-height: 36px;
        width: 25%;
        text-align: center;
        background: #f4f4f4;
        color: #555;
        position: relative;
        transition: 0.25s background ease;
        cursor: pointer;
    }

    .tabs label>span {
        margin-left: 5px;
    }

    .tabs label::after {
        content: "";
        height: 2px;
        width: 100%;
        position: absolute;
        display: block;
        background: #ccc;
        bottom: 0;
        opacity: 0;
        left: 0;
        transition: 0.25s ease;
    }

    .tabs label:hover::after {
        opacity: 1;
    }

    .tabs .line {
        position: absolute;
        height: 2px;
        background: #1E88E5;
        width: 25%;
        top: 34px;
        left: 0;
        transition: 0.25s ease;
    }

    .tabs .content-container {
        display: flex;
        background: #eee;
        /* position: relative;
        height: 100px; */
        font-size: 16px;
    }

    .tabs .content-container .content {
        padding: 10px;
        /* position: absolute;
        width: 100%;
        top: 0;
        opacity: 0; */
        color: #333;
        display: block;
        visibility: hidden;
        margin-right: -100%;
        width: 100%;
    }

    .tabs .content-container .content h3 {
        font-weight: 200;
        margin: 10px 0;
    }

    .tabs .content-container .content p {
        margin: 10px 0;
    }

    .tabs .content-container .content p,
    .tabs .content-container .content i {
        font-size: 13px;
    }

    @media screen and (max-width:600px) {
        .tabs label>span {
            display: none;
        }
    }
</style>
<?php get_sidebar(); ?>
<?php get_footer(); ?>