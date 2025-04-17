<?php
$selectedTshirt = $recordSet1->fields['tshirt'] ; // <- твоя вибрана футболка з БД
?>
<style>
.custom-select {
  position: relative;
  width: 250px;
  margin-bottom: 20px;
}

.custom-select .selected {
  padding: 10px;
  border: 1px solid #ccc;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.custom-select .selected img {
  width: 40px;
  margin-right: 10px;
}

.custom-select .options {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  border: 1px solid #ccc;
  border-top: none;
  background: white;
  max-height: 250px;
  overflow-y: auto;
  display: none;
  z-index: 1000;
}

.custom-select .option {
  padding: 10px;
  display: flex;
  align-items: center;
  cursor: pointer;
}

.custom-select .option:hover {
  background: #f0f0f0;
}

.custom-select .option img {
  width: 40px;
  margin-right: 10px;
}
</style>

<div class="custom-select" id="tshirtSelect">
  <div class="selected" id="selectedOption">
    <img src="/img/t-shirt/<?= htmlspecialchars($selectedTshirt) ?>" alt="">
    <span><?= basename(htmlspecialchars($selectedTshirt)) ?></span>
    <svg class="chevron" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="gray" viewBox="0 0 24 24">
      <path d="M7 10l5 5 5-5z"/>
    </svg>
  </div>

  <div class="options" id="optionList">
    <?php foreach ($tshirtFiles as $file):
      $fileName = basename($file);
    ?>
      <div class="option" data-value="<?= htmlspecialchars($fileName) ?>">
        <img src="/img/t-shirt/<?= htmlspecialchars($fileName) ?>" alt="">
        <span><?= htmlspecialchars($fileName) ?></span>
      </div>
    <?php endforeach; ?>
  </div>

  <input type="hidden" name="tshirt_select" id="selectedValue" value="<?= htmlspecialchars($selectedTshirt) ?>">
</div>

<script>
const selected = document.getElementById('selectedOption');
const optionList = document.getElementById('optionList');
const options = document.querySelectorAll('.custom-select .option');
const hiddenInput = document.getElementById('selectedValue');

selected.addEventListener('click', () => {
  optionList.style.display = optionList.style.display === 'block' ? 'none' : 'block';
});

options.forEach(option => {
  option.addEventListener('click', () => {
    const imgSrc = option.querySelector('img').getAttribute('src');
    const text = option.querySelector('span').textContent;
    selected.innerHTML = `
      <img src="${imgSrc}" alt="">
      <span>${text}</span>
      <svg class="chevron" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="gray" viewBox="0 0 24 24">
        <path d="M7 10l5 5 5-5z"/>
      </svg>`;
    hiddenInput.value = option.dataset.value;
    optionList.style.display = 'none';
  });
});

document.addEventListener('click', (e) => {
  if (!document.getElementById('tshirtSelect').contains(e.target)) {
    optionList.style.display = 'none';
  }
});
</script>
