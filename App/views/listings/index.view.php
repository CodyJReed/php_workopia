<?php loadViewPartial('head'); ?>
<?php loadViewPartial('navbar'); ?>
<?php loadViewPartial('message'); ?>
<div class="container mx-auto p-4 mt-4">
  <div class="text-center text-3xl mb-4 font-bold border border-gray-300 p-3">
    <?php
    /** @var string $keywords */
    /** @var string $location */
    if ($keywords) : ?>
      Search results for: <?= htmlspecialchars($keywords) ?>
    <?php elseif ($location) : ?>
      Search results for: <?= htmlspecialchars($location) ?>
    <?php else : ?>
      Recent Jobs
    <?php endif ?>
  </div>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 mt-4 p-3">
    <!-- Job Listing 1: Software Engineer -->
    <?php
    /** @var array $listings */
    foreach ($listings as $listing) :
    ?>
      <div class="rounded-lg shadow-md bg-white">
        <div class="p-4 flex flex-col h-full">
          <h2 class="text-xl font-semibold"><?= $listing->title ?></h2>
          <p class="text-gray-700 text-lg mt-2"><?= $listing->description ?>
          </p>
          <ul class="my-4 bg-gray-100 p-4 rounded">
            <?php foreach ($listing as $key => $value) : ?>
              <!-- Salary -->
              <?php if (strtolower($key) === 'salary' && $value) : ?>
                <li class="mb-2">
                  <strong><?= ucwords($key) ?>:</strong> $<?= number_format($value, 0, '', ',') ?>
                </li>
              <?php endif; ?>
              <!-- Location -->
              <?php if (strtolower($key) === 'city' && $value) : ?>
                <li class="mb-2">
                  <strong>Location:</strong> <?= ucwords($value) ?>
                </li>
              <?php endif; ?>
            <?php endforeach; ?>
            <?php foreach ($listing as $key => $value) : ?>
              <!-- Tags -->
              <?php if (strtolower($key) === 'tags' && $value) : ?>
                <li class="mb-2">
                  <strong><?= ucwords($key) ?>:</strong> <?= $value ?>
                </li>
              <?php endif; ?>
            <?php endforeach; ?>
          </ul>
          <a href="/listings/<?= $listing->id ?>"
            class="block w-full text-center px-5 py-2.5 shadow-sm rounded border text-base font-medium text-indigo-700 bg-indigo-100 hover:bg-indigo-200 mt-auto">
            Details
          </a>
        </div>
      </div>
    <?php endforeach ?>
  </div>
</div>
</div>
<?php loadViewPartial('bottom-banner'); ?>
<?php loadViewPartial('footer'); ?>