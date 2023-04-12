
<?php
/**
 * Plugin Name: Contact Form
 * Description:  formulaire de contact .
 * Author:DOUAE LINJRI
 * Version: 1.0
 */


    function contact_form_add_menu_item()
    {
        add_menu_page(
            'Contact Form',
            'Contact Form',
            'manage_options',
            'contact-form',
            'contact_form_display_page'
        );
    }
    
    add_action('admin_menu', 'contact_form_add_menu_item');


    function contact_form_create_table()
    {
        global $wpdb;
        $wp_contact_form = $wpdb->prefix . 'contact_form';
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $wp_contact_form (
                id INT(9) NOT NULL AUTO_INCREMENT,
                subject VARCHAR(200) NOT NULL,
                name VARCHAR(200) NOT NULL,
                last_name VARCHAR(200) NOT NULL,
                email VARCHAR(200) NOT NULL,
                message VARCHAR(300) NOT NULL,
                date_envoi DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
                PRIMARY KEY  (id)
            ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
        dbDelta($sql);
    }
        register_activation_hook(__FILE__, 'contact_form_create_table');
        register_deactivation_hook(__FILE__, 'my_plugin_deactivation');

    function my_plugin_deactivation()
    {
        global $wpdb;
        $wp_contact_form = $wpdb->prefix . 'contact_form';
        $wpdb->query("DROP TABLE IF EXISTS $wp_contact_form");
    }

    function my_plugin_enqueue_styles() {
        wp_enqueue_style( 'my-plugin-styles', plugin_dir_url( __FILE__ ) . 'style.css' );
    }
    add_action( 'wp_enqueue_scripts', 'my_plugin_enqueue_styles' );




    function contact_form_shortcode() {
        // Votre code de traitement du formulaire ici
        // Insérez le code pour enregistrer les données soumises dans la base de données
        // Utilisez la fonction wp_mail() pour envoyer un email de notification
        
        // Affiche le formulaire de contact
        ob_start();
        ?>
        <form id="contact-form" method="post" action="">
            <label for="name">Nom :</label>
            <input type="text" name="name" required>
            <label for="last_name">Prenom :</label>
            <input type="text" name="last_name" required><br>
            <label for="email">Email :</label>
            <input type="email" name="email" required><br>
            <label for="subject">Sujet :</label>
            <input type="text" name="subject" required>
            <label for="message">Message :</label>
            <textarea name="message" required></textarea><br>
            <input type="submit" value="Envoyer"name="submitcontact" >
        </form>
        <?php
        return ob_get_clean();
    }
    add_shortcode('form', 'contact_form_shortcode');



    function execute_on_init_event(){
        if(isset($_POST["submitcontact"])){
            $sujet = $_POST["subject"];
            $nom = $_POST["name"];
            $prenom = $_POST["last_name"];
            $email = $_POST["email"];
            $message = $_POST["message"];
            global $wpdb;
    $date= current_time('mysql');
    $table = $wpdb->prefix . 'contact_form';
    $data = array('id' => NULL,'date_envoi' => $date, 'name' => $nom, 'last_name' => $prenom, 'email' => $email, 'subject' => $sujet, 'message' => $message );
    $wpdb->insert($table,$data);
    $result = $wpdb->insert($table, $data);
    
    if ($result) {
        echo '<script>alert("Data inserted successfully.")</script>';
    } else {
        echo '<script>alert("There was an error inserting the data.")</script>';
    }
        }
    }
    // add the action
    add_action( "init", "execute_on_init_event");

        function contact_form_display_page()
        {
        global $wpdb;
        
        $wp_contact_form = $wpdb->prefix . 'contact_form';
        
        $results = $wpdb->get_results( "SELECT * FROM $wp_contact_form" );
        
        echo '<h1>Contact form</h1>';
        echo '<table class="mytable">';
        echo ' <thead>';
        echo "<tr><th>Nom</th><th>Prenom</th><th>Email</th><th>Sujet</th><th>Message</th><th>Date d'envoie</th></tr>";
        echo " </thead>";
        echo " <tbody>";
        foreach ( $results as $row ) {
            echo '<tr>';
            echo '<td>' . $row->name . '</td>';
            echo '<td>' . $row->last_name . '</td>';
            echo '<td>' . $row->email . '</td>';
            echo '<td>' . $row->subject . '</td>';
            echo '<td>' . $row->message . '</td>';
            echo '<td>' . $row->date_envoi . '</td>';
            echo '</tr>';
        }
        echo " </tbody>";
        echo '</table>';
        }

    
?>