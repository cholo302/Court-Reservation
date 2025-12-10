<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Search & Filters -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
        <form action="<?= url('courts') ?>" method="GET" class="grid md:grid-cols-5 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <input type="text" name="q" value="<?= htmlspecialchars($query) ?>" 
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                        placeholder="Search courts, locations...">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Court Type</label>
                <select name="type" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                    <option value="">All Types</option>
                    <?php foreach ($courtTypes as $type): ?>
                    <option value="<?= $type['slug'] ?>" <?= ($filters['court_type'] ?? '') === $type['slug'] ? 'selected' : '' ?>>
                        <?= $type['name'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                <select name="city" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                    <option value="">All Cities</option>
                    <?php foreach ($cities as $city): ?>
                    <?php if ($city): ?>
                    <option value="<?= e($city) ?>" <?= ($filters['city'] ?? '') === $city ? 'selected' : '' ?>>
                        <?= e($city) ?>
                    </option>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full bg-ph-blue text-white py-2 rounded-lg font-medium hover:bg-blue-800 transition">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
            </div>
        </form>
    </div>
    
    <!-- Results -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">
            <?= count($courts) ?> Courts Found
            <?php if ($query): ?>
                <span class="text-gray-500 font-normal">for "<?= htmlspecialchars($query) ?>"</span>
            <?php endif; ?>
        </h1>
        
        <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-500">Sort by:</span>
            <select class="border border-gray-300 rounded-lg px-3 py-1 text-sm">
                <option>Rating</option>
                <option>Price: Low to High</option>
                <option>Price: High to Low</option>
            </select>
        </div>
    </div>
    
    <?php if (empty($courts)): ?>
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
        <h2 class="text-xl font-semibold text-gray-700 mb-2">No courts found</h2>
        <p class="text-gray-500 mb-4">Try adjusting your search or filters</p>
        <a href="<?= url('courts') ?>" class="text-ph-blue hover:underline">Clear all filters</a>
    </div>
    <?php else: ?>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($courts as $court): ?>
        <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition group">
            <div class="relative h-48 bg-gradient-to-br from-ph-blue to-blue-700">
                <?php if ($court['thumbnail']): ?>
                <img src="<?= url($court['thumbnail']) ?>" alt="<?= $court['name'] ?>" class="w-full h-full object-cover">
                <?php else: ?>
                <div class="w-full h-full flex items-center justify-center">
                    <i class="fas fa-basketball-ball text-white/30 text-6xl"></i>
                </div>
                <?php endif; ?>
                
                <!-- Badge -->
                <span class="absolute top-3 left-3 bg-white/90 text-ph-blue text-xs font-semibold px-2 py-1 rounded">
                    <?= $court['court_type_name'] ?? 'Court' ?>
                </span>
                
                <?php if (!empty($court['peak_hour_rate'])): ?>
                <span class="absolute top-3 right-3 bg-ph-yellow text-ph-blue text-xs font-semibold px-2 py-1 rounded">
                    Peak Hours Available
                </span>
                <?php endif; ?>
            </div>
            
            <div class="p-5">
                <div class="flex items-start justify-between mb-2">
                    <h3 class="font-semibold text-gray-900 group-hover:text-ph-blue transition"><?= $court['name'] ?></h3>
                    <?php if ($court['rating'] > 0): ?>
                    <div class="flex items-center text-sm">
                        <i class="fas fa-star text-yellow-400 mr-1"></i>
                        <span><?= number_format($court['rating'], 1) ?></span>
                        <span class="text-gray-400 ml-1">(<?= $court['total_reviews'] ?>)</span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <p class="text-gray-500 text-sm mb-3">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    <?= $court['location'] ?>, <?= $court['city'] ?>
                </p>
                
                <?php if ($court['capacity']): ?>
                <p class="text-gray-500 text-sm mb-3">
                    <i class="fas fa-users mr-1"></i>
                    Up to <?= $court['capacity'] ?> players
                </p>
                <?php endif; ?>
                
                <!-- Amenities -->
                <?php $amenities = json_decode($court['amenities'] ?? '[]', true); ?>
                <?php if (!empty($amenities)): ?>
                <div class="flex flex-wrap gap-2 mb-4">
                    <?php foreach (array_slice($amenities, 0, 4) as $amenity): ?>
                    <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">
                        <?php
                        $icon = match($amenity) {
                            'parking' => 'fa-parking',
                            'shower' => 'fa-shower',
                            'locker' => 'fa-lock',
                            'lights' => 'fa-lightbulb',
                            'aircon' => 'fa-snowflake',
                            default => 'fa-check'
                        };
                        ?>
                        <i class="fas <?= $icon ?> mr-1"></i>
                        <?= ucfirst($amenity) ?>
                    </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <div class="flex items-center justify-between pt-4 border-t">
                    <div>
                        <span class="text-ph-blue font-bold text-xl"><?= formatPrice($court['hourly_rate']) ?></span>
                        <span class="text-gray-400 text-sm">/hour</span>
                    </div>
                    <a href="<?= url('courts/' . $court['id']) ?>" class="bg-ph-blue text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-800 transition">
                        View Details
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
