<p>Select collection</p>
<form action="/settings.php" method="POST">
  <?php if (!empty($formCollection)): ?>
    <?php foreach ($formCollection as $k => $v): ?>
      <label>
        <input type="checkbox" name="collection-<?php print $k;?>" value="<?php print $k ?>">
        <?php print $v ?>
      </label>
      <br>
    <?php endforeach; ?>
  <?php else: ?>
    <p>No collection found.</p>
  <?php endif; ?>
  <p><input type="submit"></p>
</form>
