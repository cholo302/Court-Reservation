<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Court Management</h1>
    <a href="<?= url('admin/courts/create') ?>" class="bg-ph-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
        <i class="fas fa-plus mr-2"></i>Add New Court
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="<?= $_GET['search'] ?? '' ?>" placeholder="Search courts..." 
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
        </div>
        <div>
            <select name="type" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                <option value="">All Types</option>
                <?php foreach ($courtTypes as $type): ?>
                <option value="<?= $type['id'] ?>" <?= ($_GET['type'] ?? '') == $type['id'] ? 'selected' : '' ?>>
                    <?= $type['name'] ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <select name="status" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                <option value="">All Status</option>
                <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($_GET['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                <option value="maintenance" <?= ($_GET['status'] ?? '') === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
            </select>
        </div>
        <button type="submit" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition">
            <i class="fas fa-search mr-2"></i>Filter
        </button>
    </form>
</div>

<!-- Courts Grid -->
<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($courts as $court): ?>
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="h-48 bg-gray-200 relative">
            <?php if (!empty($court['thumbnail'])): ?>
            <img src="<?= url($court['thumbnail']) ?>" alt="<?= $court['name'] ?>" class="w-full h-full object-cover">
            <?php else: ?>
            <div class="w-full h-full flex items-center justify-center">
                <i class="fas fa-basketball-ball text-gray-400 text-4xl"></i>
            </div>
            <?php endif; ?>
            
            <span class="absolute top-3 right-3 px-2 py-1 rounded-full text-xs font-semibold <?php
                echo $court['is_active'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
            ?>">
                <?= $court['is_active'] ? 'Active' : 'Inactive' ?>
            </span>
        </div>
        
        <div class="p-4">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-lg"><?= $court['name'] ?></h3>
                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded"><?= $court['court_type_name'] ?></span>
            </div>
            
            <p class="text-gray-500 text-sm mb-3"><?= $court['location'] ?></p>
            
            <div class="flex justify-between items-center text-sm mb-3">
                <span class="text-gray-600">
                    <i class="fas fa-peso-sign mr-1"></i>
                    <?= formatPrice($court['hourly_rate']) ?>/hour
                </span>
                <span class="text-gray-600">
                    <i class="fas fa-users mr-1"></i>
                    <?= $court['capacity'] ?? 10 ?> max players
                </span>
            </div>
            
            <div class="flex space-x-2">
                <a href="<?= url('admin/courts/' . $court['id'] . '/edit') ?>" 
                   class="flex-1 text-center bg-ph-blue/10 text-ph-blue py-2 rounded-lg hover:bg-ph-blue/20 transition">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
                <a href="<?= url('admin/courts/' . $court['id'] . '/schedule') ?>" 
                   class="flex-1 text-center bg-green-100 text-green-700 py-2 rounded-lg hover:bg-green-200 transition">
                    <i class="fas fa-clock mr-1"></i>Schedule
                </a>
                <form action="<?= url('admin/courts/' . $court['id'] . '/delete') ?>" method="POST" class="inline"
                      onsubmit="return confirm('Are you sure you want to delete this court?')">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    <button type="submit" class="px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if (empty($courts)): ?>
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="fas fa-basketball-ball text-gray-300 text-5xl mb-4"></i>
    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Courts Found</h3>
    <p class="text-gray-500 mb-4">Get started by adding your first court.</p>
    <a href="<?= url('admin/courts/create') ?>" class="inline-block bg-ph-blue text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
        <i class="fas fa-plus mr-2"></i>Add Court
    </a>
</div>
<?php endif; ?>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div class="flex justify-center mt-8">
    <nav class="flex space-x-2">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>&<?= http_build_query(array_diff_key($_GET, ['page' => ''])) ?>" 
           class="px-4 py-2 rounded-lg <?= $currentPage === $i ? 'bg-ph-blue text-white' : 'bg-white text-gray-700 hover:bg-gray-100' ?>">
            <?= $i ?>
        </a>
        <?php endfor; ?>
    </nav>
</div>
<?php endif; ?>
