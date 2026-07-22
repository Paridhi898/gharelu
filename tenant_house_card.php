<?php
/* Reusable property card — expects $house (array) and $tab (string) in scope.
   Included from tenant_dashboard.php inside a foreach loop. */
?>
<div class="property-card">
    <div class="property-image">
        <?php if ($img = img_or_placeholder($house['image_url'])): ?>
            <img src="<?= $img ?>" alt="<?= h($house['title']) ?>">
        <?php else: ?>
            <div class="no-img"><i class="fas fa-image"></i></div>
        <?php endif; ?>
        <span class="property-status <?= h($house['availability_status']) ?>"><?= h($house['availability_status']) ?></span>

        <form method="POST">
            <input type="hidden" name="action" value="<?= $house['is_fav'] ? 'remove_favorite' : 'add_favorite' ?>">
            <input type="hidden" name="house_id" value="<?= (int)$house['house_id'] ?>">
            <input type="hidden" name="return_tab" value="<?= h($tab) ?>">
            <button type="submit" class="fav-toggle-btn <?= $house['is_fav'] ? 'is-fav' : '' ?>" title="<?= $house['is_fav'] ? 'Remove from favorites' : 'Save to favorites' ?>">
                <i class="fa<?= $house['is_fav'] ? 's' : 'r' ?> fa-heart"></i>
            </button>
        </form>
    </div>
    <div class="property-details">
        <h3><?= h($house['title']) ?></h3>
        <div class="property-location"><i class="fas fa-location-dot"></i> <?= h($house['city']) ?></div>
        <div class="property-features">
            <span><i class="fas fa-bed"></i><?= (int)$house['bedrooms'] ?> Bed</span>
            <span><i class="fas fa-bath"></i><?= (int)$house['bathrooms'] ?> Bath</span>
            <span><i class="fas fa-couch"></i><?= h($house['furnishing']) ?></span>
        </div>
        <div class="property-price">Rs <?= number_format($house['price']) ?> <small>/ month</small></div>
        <div class="property-actions">
            <button class="btn-primary" onclick="openInterestModal(<?= (int)$house['house_id'] ?>, '<?= h(addslashes($house['title'])) ?>')">
                <i class="fas fa-paper-plane"></i> Send Interest
            </button>
        </div>
    </div>
</div>