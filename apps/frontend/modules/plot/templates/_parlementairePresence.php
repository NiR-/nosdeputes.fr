<?php if (!isset($options['plot']) || $options['plot'] != 'total') { ?>
<div>
<h1>Graphes d'activité de <?php echo $parlementaire->getNom(); ?></h1>
<a href="<?php echo url_for('@plot_parlementaire_presences?slug='.$parlementaire->slug.'&time=lastyear'); ?>">Les 12 derniers mois</a><?php
   foreach ($sessions as $s) {
   echo ', <a href="'.url_for('@plot_parlementaire_presences?slug='.$parlementaire->slug.'&time='.$s['session']).'"> la session '.preg_replace('/^(\d{4})/', '\\1-', $s['session']).'</a>';
 }?>
</div>
<?php }
$plotarray = array('parlementaire' => $parlementaire, 'labels' => $labels, 'vacances' => $vacances, 'time' => 'lastyear');
if (isset($options['session'])) $plotarray['time'] = $options['session'];
if (isset($options['questions'])) $plotarray = array_merge($plotarray, array('n_questions' => $n_questions));
if (isset($options['link'])) $plotarray = array_merge($plotarray, array('link' => $options['link']));
if (isset($options['fonctions'])) $plotarray = array_merge($plotarray, array('fonctions' => $fonctions));
if (!isset($options['plot'])) $options = array_merge($options, array('plot' => 'total'));
$n = count($labels);
$presences = array_fill(1, $n, 0);
$participations = array_fill(1, $n, 0);
$mots = array_fill(1, $n, 0);
if ($options['plot'] == 'all' || $options['plot'] == 'total') {
  $plotarray = array_merge($plotarray, array('type' => 'total'));
  for ($i = 1; $i < $n; $i++) {
    $presences[$i] = $n_presences['hemicycle'][$i] + $n_presences['commission'][$i];
    $participations[$i] = $n_participations['hemicycle'][$i] + $n_participations['commission'][$i];
    $mots[$i] = $n_mots['hemicycle'][$i] + $n_mots['commission'][$i];
  }
  $plotarray = array_merge($plotarray, array('participations' => $participations, 'presences' => $presences, 'mots' => $mots));
  echo include_partial('plot/plotParlementairePresence', $plotarray);
}
if ($options['plot'] == 'all' || $options['plot'] == 'hemicycle') {
  if (!isset($plotarray['type'])) {
    $plotarray = array_merge($plotarray, array('type' => 'hemicycle'));
    $plotarray = array_merge($plotarray, array('participations' => $n_participations['hemicycle'], 'presences' => $n_presences['hemicycle'], 'mots' => $n_mots['hemicycle']));
  } else {
    $plotarray['type'] = 'hemicycle';
    $plotarray['participations'] = $n_participations['hemicycle'];
    $plotarray['presences'] = $n_presences['hemicycle'];
    $plotarray['mots'] = $n_mots['hemicycle'];
  }
  echo include_partial('plot/plotParlementairePresence', $plotarray);
}
if ($options['plot'] == 'all' || $options['plot'] == 'commission') {
  if (!isset($plotarray['type'])) {
    $plotarray = array_merge($plotarray, array('type' => 'commission'));
    $plotarray = array_merge($plotarray, array('participations' => $n_participations['commission'], 'presences' => $n_presences['commission'], 'mots' => $n_mots['commission']));
  } else {
    $plotarray['type'] = 'commission';
    $plotarray['participations'] = $n_participations['commission'];
    $plotarray['presences'] = $n_presences['commission'];
    $plotarray['mots'] = $n_mots['commission'];
  }
  if (!isset($plotarray['type'])) $plotarray = array_merge($plotarray, array('type' => 'commission'));
  else $plotarray['type'] = 'commission';
  $plotarray = array_merge($plotarray, array('participations' => $n_participations['commission'], 'presences' => $n_presences['commission'], 'mots' => $n_mots['commission']));
  echo include_partial('plot/plotParlementairePresence', $plotarray);
} ?>