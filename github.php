<?php
/*
 * Plugin Name: Github Widget
 * Plugin URI: https://github.com
 * Description: Ce plugin permet de donner l'aperçu d'un repo, d'un certain nombre de commits ainsi que le nom de son propriétaire!
 * Version: 1.0
 * Author: Léo DELMAS
 * Author URI: https://github.com/leodelmas
*/
class Github_Widget extends WP_Widget
{
    // Affichage du Widget dans apparence
    public function github_widget()
    {
        $option = array(
            "classname" => "github_widget",
            "description" => "Affiche un repo, des commits et le compte du propriétaire"
        );
        $this->WP_widget("widget-github", "Widget Github", $option);
    }

    // affichage sur la page
    public function widget($args, $instance)
    {
        $tittle = apply_filters('widget_title', $instance['titre']);
        echo $args['before_widget'];
        echo $args['before_title'] . $tittle . $args['after_title'];

        // utilisation API
        require_once __DIR__ . '/vendor/autoload.php';

        $client = new Github\Client();
        $result = $client->api('repo')->commits()->all($instance['repo_user'], $instance['repo_name'], array('sha' => 'master'));?>

        <div class="plugin_img"> <!--logo Github cliquable -->
          <a href="https://github.com/"><img src="<?= plugins_url('/github_logo.png', __FILE__)?>"/><a>
        </div>

      <h5 class="repo_title"> <!-- créateur / nom du dépo -->
            <?php echo($instance['repo_user']."/".$instance['repo_name']);?>
        </h5>

        <?php
        for ($i=0; $i < $instance['nbcommit']; $i++) //liste des commits et nom du créateur
        {
          ?>
          <li class="commits_list">
            <?php echo($result[$i]['commit']['message']."\n"); ?>
            <br>
            <ul class="commit_author"><?php echo($result[$i]['commit']['author']['name']); ?> </ul>
          </li>
          <?php
        }
        wp_enqueue_style('github_style',plugins_url('styles/github.css',__FILE__)); // lien vers css
        echo $args['after_widget'];
    }

    // mise à jour du formulaire
    public function update($new, $old)
    {
        return $new;
    }

    // formulaire
    public function form($instance)
    {
        ?>
        <p>
            <label for="<?php echo $this->get_field_id("titre"); ?>">Titre : </label></br>
            <input name="<?php echo $this->get_field_name("titre"); ?>"id="<?php echo $this->get_field_id("titre"); ?>" type="text"/></br>
            <label for="<?php echo $this->get_field_id("repo_user"); ?>">Propriétaire du dépôt : </label></br>
            <input name="<?php echo $this->get_field_name("repo_user"); ?>"id="<?php echo $this->get_field_id("repo_user"); ?>" type="text"/></br>
            <label for="<?php echo $this->get_field_id("repo_name"); ?>">Nom du dépôt : </label></br>
            <input name="<?php echo $this->get_field_name("repo_name"); ?>"id="<?php echo $this->get_field_id("repo_name"); ?>" type="text"/></br>
            <label for="<?php echo $this->get_field_id("nbcommit"); ?>">Nombre de commits : </label></br>
            <input name="<?php echo $this->get_field_name("nbcommit"); ?>"id="<?php echo $this->get_field_id("nbcommit"); ?>" type="number"/></br>
        </p>
        <?php
    }
} // class Github_Widget

function register_github_widget()
{
    register_widget('Github_Widget');
}
add_action('widgets_init', 'register_github_widget');
?>
