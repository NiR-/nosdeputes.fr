<?php
$titre = "Graphes d'activité parlementaire";
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre));
?>
<?php echo include_component('plot', 'parlementaire', array('parlementaire' => $parlementaire, 'options' => array('plot' => 'all', 'fonctions' => 'on', 'questions' => 'on', 'session' => $session))); ?>
  <div class="explications" id="explications">
    <h2>Explications :</h2>
    <?php //echo link_to("Présence en séances de commission et d'hémicycle",'@parlementaire_presences?slug='.$parlementaire->getSlug()); ?>
    <p class="indent_guillemets"><a href="/faq">voir les questions fréquentes (rubrique FAQ)</a>
  </div>