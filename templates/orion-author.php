<?php get_header();

global $wpdb;

$curauth            = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
$wpamelia_provider  = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}amelia_users WHERE type = 'provider' AND externalId = $curauth->ID", OBJECT);
$tipocoach          = (isset($_GET["tipocoaching"])) ? '&tipocoaching=' . $_GET["tipocoaching"] : '';
$precio             = (isset($_GET["precio"])) ? '&precio=' . $_GET["precio"] : '';
$long_description   = get_user_meta($curauth->ID, 'long_description', true);
$long_description   = htmlspecialchars_decode($long_description);
$long_description   = wpautop($long_description);
$user_certification = get_user_meta($curauth->ID, 'icf_certification', true);
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
                        <div class="wp-block-buttons is-content-justification-center is-layout-flex wp-container-2">
                            <div class="wp-block-button">
                                <a class="wp-block-button__link has-text-align-center wp-element-button" href="/pedir-cita-coach/?coach=<?php echo $wpamelia_provider[0]->firstName  . ' ' . $wpamelia_provider[0]->lastName; ?><?php echo $tipocoach; ?><?php echo $precio; ?>">
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
                            <label for="tab1"><i class="fa fa-code"></i> Certificación ICF</label>
                            <input type="radio" id="tab2" name="tab">
                            <label for="tab2"><i class="fa fa-history"></i> Sobre mi</label>
                            <input type="radio" id="tab3" name="tab">
                            <label for="tab3"><i class="fa fa-pencil"></i> Reviews</label>
                            <input type="radio" id="tab4" name="tab">
                            <label for="tab4"><i class="fa fa-share-alt"></i> Share</label>
                            <div class="line"></div>
                            <div class="content-container">
                                <div class="content" id="c1">
                                    <h3>Certificación ICF</h3>
                                    <?php echo $user_certification; ?>
                                </div>
                                <div class="content" id="c2">
                                    <h3>Sobre mi</h3>
                                    <?php echo $long_description; ?>
                                </div>
                                <div class="content" id="c3">
                                    <h3>Reviews</h3>
                                    <p>Amazing product. I don't know how it works.</p>
                                    <i>- Anonymous</i>
                                </div>
                                <div class="content" id="c4">
                                    <h3>Share</h3>
                                    <p>This product is currently not shareable.</p>
                                </div>
                            </div>
                        </div>
                        Nombre y Apellidos
                        Foto
                        Video de presentación
                        Certificación ICF: ACC, PCC, MCC (desplegable y si no puesto entre paréntesis para que sepan las opciones que tienen que elegir)
                        e-mail de contacto:
                        Teléfono de Contacto:
                        Descripción breve (Describe en máximo 140 caracteres qué te hace diferente, por qué tendrían que elegirte a ti como coach)
                        Descripción larga (Describe en una extensión máxima de 200 palabras, quién eres, trayectoria, especialización, que te hace diferente, dónde puedes aportar más valor… es decir, todo lo que te defina y pueda dar información al coachee para saber si le gustaría hacer un proceso de coaching contigo)
                        Objetivos de coaching en los que puedes ayudar (las categorías para que marquen los tipos que ellos hacen. Personal/de Vida….)
                        Tipo de coaching: individual y/o equipos
                        Precio proceso de coaching (5 sesiones online de 1h cada una): (Numérico libre. Importe mínimo 250 euros)



                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
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
    }

    .tabs input#tab2:checked~.line {
        left: 25%;
    }

    .tabs input#tab2:checked~.content-container #c2 {
        opacity: 1;
    }

    .tabs input#tab3:checked~.line {
        left: 50%;
    }

    .tabs input#tab3:checked~.content-container #c3 {
        opacity: 1;
    }

    .tabs input#tab4:checked~.line {
        left: 75%;
    }

    .tabs input#tab4:checked~.content-container #c4 {
        opacity: 1;
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
        background: #eee;
        position: relative;
        height: 100px;
        font-size: 16px;
    }

    .tabs .content-container .content {
        position: absolute;
        padding: 10px;
        width: 100%;
        top: 0;
        opacity: 0;
        transition: 0.25s ease;
        color: #333;
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
</style>
<?php get_sidebar(); ?>
<?php get_footer(); ?>