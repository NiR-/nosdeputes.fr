<?php use_helper('Text') ?>
<?php echo '<div class="precedent">'.myTools::displayDate($loi->date).'</div>'; ?>
<?php if (isset($dossier)) echo '<div class="source">'.link_to('Dossier relatif', '@section?id='.$dossier)."</div>"; ?>
<div class="loi"><h1><?php echo $loi->titre; ?></h1>
<?php if (isset($doc) && $doc != null) echo "<h2>".$doc->getShortTitreExplained()."</h2>"; ?>
</div>
<?php if ($loi->source) echo '<p class="source"><a href="'.$loi->source.'" rel="nofollow">Source</a></p><div class="clear"></div>';
if ($loi->expose) {
  echo '<div class="loi"><h2>Exposé des motifs&nbsp;:</h2>';
  if ($loi->parlementaire_id) {
    echo '<div class="intervenant">';
    $perso = $loi->getParlementaire();
    if ($perso->getPageLink() && $photo = $perso->hasPhoto()) {
      echo '<a href="'.url_for($perso->getPageLink()).'">';
      include_partial('parlementaire/photoParlementaire', array('parlementaire' => $perso, 'height' => 70));
      echo '</a>';
    }
    echo '</div>';
  }
  $expose = myTools::escape_blanks($loi->expose);
  $pos = 0;
  for ($i=0; $i < 8; $i++)
    $pos = strpos($expose, '<', $pos+1);
  if ($pos) {
    echo substr($expose, 0, $pos - 1);
    echo '<div id="expose_court">Lire l\'exposé complet...</div>';
    echo '<div id="expose_complet">'.substr($expose, $pos).'</div>';
  } else {
    echo $expose;
  }
  if ($loi->parlementaire_id) 
    echo '<div class="auteurloi"><a href="'.url_for($perso->getPageLink()).'">'.$perso->nom.'</a></div>';
  echo '<br/><h2>Sommaire&nbsp;:</h2></div>';
} ?>
<div class="sommaireloi">
<?php $nart = 0; $nbart = 0;
if (isset($soussections) && count($soussections)) {
  $level = 0;
  foreach ($soussections as $ss) {
    if ($ss->level <= $level) {
      echo '<br/><small> &nbsp; Article';
      if ($nbart > 1) echo 's&nbsp;:';
      echo ' ';
      for ($i=$nart;$i<$nart+$nbart;$i++) {
        echo link_to($articles[$i]['titre'], '@loi_article?loi='.$loi->texteloi_id.'&article='.$articles[$i]['slug']);
        if ($i != $nart+$nbart-1) echo ', ';
      }
      $nart += $nbart;
      echo '</small>';
      if ($ss->level < $level) for ($i=0; $i < $level-$ss->level; $i++)
        echo "</li></ul>";
      else echo "</li>";
    } else {
      echo "<ul>";
      if (($loi->texteloi_id ==  1109 || $loi->texteloi_id == 1005) && $nart == 0 && $nbart > 0) {
        echo "<small> &nbsp; Article ".link_to($articles[0]['titre'], '@loi_article?loi='.$loi->texteloi_id.'&article='.$articles[0]['slug']).'</small>';
        $nart = 1;
      }
    }
    echo '<li class="level'.$ss->getLevel().'">'.link_to($ss->getLevelTitre(), $ss->getUrl());
    $level = $ss->getLevel();
    $nbart = $ss->nb_articles;
    if ($ss->nb_commentaires > 0 || $amendements_sec[$ss->id]) {
      echo ' (';
      if ($amendements_sec[$ss->id])
        echo '<span class="orange">'.$amendements_sec[$ss->id].'&nbsp;amendement'.($amendements_sec[$ss->id] > 1 ? 's' : '').'</span>';
      if ($ss->nb_commentaires > 0 && $amendements_sec[$ss->id]) echo ", ";
      if ($ss->nb_commentaires > 0)
        echo '<span class="coms_loi_txt">'.$ss->nb_commentaires.'&nbsp;commentaire'.($ss->nb_commentaires > 1 ? 's' : '').'</span>';
      echo ')';
    }
  }
  echo '<br/><small> &nbsp; Article';
  if ($nbart > 1) echo 's';
  echo '&nbsp;: ';
  for ($i=$nart;$i<$nart+$nbart;$i++) {
    echo link_to($articles[$i]['titre'], '@loi_article?loi='.$loi->texteloi_id.'&article='.$articles[$i]['slug']);
    if ($i != $nart+$nbart-1) echo ', ';
  }
  echo '</small>';
  if ($ss->level < $level) for ($i=0; $i < $level-$ss->level; $i++)
    echo "</li></ul>";
  if ($amendements) {
    echo '<p class="suivant">'.link_to('Voir les '.$amendements.' amendements déposés sur ce texte', '@find_amendements_by_loi_and_numero?loi='.$loi->texteloi_id.'&numero=all');
    if (file_exists('liasses/liasse_'.$loi->texteloi_id.'.pdf')) echo '<br/>(<a href="/liasses/liasse_'.$loi->texteloi_id.'.pdf">version imprimable</a>)';
    echo '</p>';
  }
} else {
  foreach ($articles as $a) {
    if ($nart != 0) echo '</li>';
    else echo '<ul>';
    $nart = $a['ordre'];
    echo '<li><b><a href="'.url_for('@loi_article?loi='.$loi->texteloi_id.'&article='.$a['slug']).'">';
    echo 'Article '.$a['titre'];
    if (isset($a['expose'])) echo '&nbsp;:</b>&nbsp;'.myTools::escape_blanks(truncate_text(preg_replace('/<\/?p>|\&[^\;]+\;/i', ' ', strip_tags($a['expose'])), 120));
    echo '</a>';
    $atitre = strtolower($a['titre']);
    $nadmts = 0;
    if (isset($amendements_art[$atitre.'tot'])) $nadmts += $amendements_art[$atitre.'tot'];
    if (isset($amendements_art['après '.$atitre.'tot'])) $nadmts += $amendements_art['après '.$atitre.'tot'];
    if (isset($amendements_art['avant '.$atitre.'tot'])) $nadmts += $amendements_art['avant '.$atitre.'tot'];
    if ($a['nb_commentaires'] > 0 || $nadmts > 0) {
      echo '&nbsp; (';
      if ($nadmts)
        echo '<span class="orange">'.$nadmts.'&nbsp;amendement'.($nadmts > 1 ? 's' : '').'</span>';
      if ($a['nb_commentaires'] > 0 && $nadmts) echo ", ";
      if ($a['nb_commentaires'] > 0)
        echo '<span class="coms_loi_txt">'.$a['nb_commentaires'].'&nbsp;commentaire'.($a['nb_commentaires'] > 1 ? 's' : '').'</span>';
      echo ')';
    }
  }
} ?>
</div>
<br/>
<div class="commentaires">
  <h3>Derniers commentaires sur <?php echo preg_replace('/<br\/>.*$/', '', $loi->titre); ?> <span class="rss"><a href="<?php echo url_for('@loi_rss_commentaires?loi='.$loi->texteloi_id); ?>"><?php echo image_tag('xneth/rss.png', 'alt="Flux rss"'); ?></a></span></h3>
<?php if ($loi->nb_commentaires == 0) echo '<p>Cette loi n\'a pas encore inspiré de commentaire aux utilisateurs.</p>';
else {
  echo include_component('commentaire', 'lastObject', array('object' => $loi, 'presentation' => 'noloi'));
  if ($loi->nb_commentaires > 4)
    echo '<p class="suivant">'.link_to('Voir les '.$loi->nb_commentaires.' commentaires', '@loi_commentaires?loi='.$loi->texteloi_id).'</p><div class="stopfloat"></div>';
} ?>
</div>
