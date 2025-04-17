<style>
.shirt-selector{
    display: flex;
    gap: 10px;
    justify-content: center;
}
.shirt-selector-wrapper {
    margin-bottom: 20px;
}
.shirt-select {
    position: relative;
    width: 100px;
    cursor: pointer;
}
.shirt-preview {
    width: 100%;
    height: auto;
    border: 2px solid #ccc;
    border-radius: 4px;
}
.shirt-options {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 999;
    background: white;
    border: 1px solid #ccc;
    padding: 5px;
    width: 100px;
    max-height: 250px;
    overflow-y: auto;
}
.shirt-option {
    padding: 2px;
    cursor: pointer;
}
.shirt-option img {
    width: 100%;
    height: auto;
    border-radius: 4px;
}
.shirt-select.open .shirt-options {
    display: block;
}
</style>

<div class="shirt-selector">
    <div class="shirt-selector-wrapper">
        <label>Футболка команды 1</label>
        <div class="shirt-select" data-input-id="shirt1">
            <img height="150" src="/img/t-shirt/<?= $currentShirt1 ?: 'gray-manish.png' ?>" class="shirt-preview selected" id="preview-shirt1">
            <div class="shirt-options">
                <?php foreach ($shirts as $shirt): ?>
                    <div class="shirt-option" data-value="<?= $shirt ?>">
                        <img height="150" src="/img/t-shirt/<?= $shirt ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <input type="hidden" name="shirt1" id="shirt1" value="<?= $currentShirt1 ?>">
    </div>

    <div class="shirt-selector-wrapper">
        <label>Футболка команды 2</label>
        <div class="shirt-select" data-input-id="shirt2">
            <img height="150" src="/img/t-shirt/<?= $currentShirt2 ?: 'gray-manish.png' ?>" class="shirt-preview selected" id="preview-shirt2">
            <div class="shirt-options">
                <?php foreach ($shirts as $shirt): ?>
                    <div class="shirt-option" data-value="<?= $shirt ?>">
                        <img height="150" src="/img/t-shirt/<?= $shirt ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <input type="hidden" name="shirt2" id="shirt2" value="<?= $currentShirt2 ?>">
    </div>
</div>
