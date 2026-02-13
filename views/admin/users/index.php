<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
</div>

<!-- Stats -->
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4">
        <p class="text-sm text-gray-500">Total Users</p>
        <p class="text-2xl font-bold text-blue-600"><?= $totalUsers ?? 0 ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4">
        <p class="text-sm text-gray-500">Active This Month</p>
        <p class="text-2xl font-bold text-green-600"><?= $stats['active_month'] ?? 0 ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4">
        <p class="text-sm text-gray-500">New This Week</p>
        <p class="text-2xl font-bold text-purple-600"><?= $stats['new_week'] ?? 0 ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4">
        <p class="text-sm text-gray-500">Blacklisted</p>
        <p class="text-2xl font-bold text-red-600"><?= $stats['blacklisted'] ?? 0 ?></p>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="<?= $_GET['search'] ?? '' ?>" 
                   placeholder="Search by name, email, phone..." 
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
        </div>
        <div>
            <select name="status" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                <option value="">All Status</option>
                <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($_GET['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                <option value="blacklisted" <?= ($_GET['status'] ?? '') === 'blacklisted' ? 'selected' : '' ?>>Blacklisted</option>
            </select>
        </div>
        <button type="submit" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition">
            <i class="fas fa-search mr-2"></i>Filter
        </button>
    </form>
</div>

<!-- Users Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Verification</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Bookings</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Joined</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php if (empty($users['items'])): ?>
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-users text-4xl mb-2"></i>
                        <p>No users found</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($users['items'] as $user): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <?php if (!empty($user['profile_image']) && file_exists(__DIR__ . '/../../../' . $user['profile_image'])): ?>
                            <img src="/<?= $user['profile_image'] ?>" alt="<?= htmlspecialchars($user['name']) ?>" 
                                class="w-10 h-10 rounded-full object-cover mr-3 border border-gray-200">
                            <?php else: ?>
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-blue-600 font-semibold"><?= strtoupper(substr($user['name'], 0, 1)) ?></span>
                            </div>
                            <?php endif; ?>
                            <div>
                                <p class="font-medium"><?= htmlspecialchars($user['name']) ?></p>
                                <p class="text-sm text-gray-500"><?= $user['role'] ?? 'user' ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm"><?= htmlspecialchars($user['email']) ?></p>
                        <?php if (!empty($user['phone'])): ?>
                        <p class="text-sm text-gray-500"><?= $user['phone'] ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <!-- ID Card Photo -->
                            <?php if (!empty($user['gov_id_photo'])): ?>
                            <div class="relative inline-block">
                                <img src="/Court-Reservation/<?= htmlspecialchars($user['gov_id_photo']) ?>" alt="ID Card" 
                                    class="w-16 h-16 rounded border-2 border-blue-300 object-cover cursor-pointer hover:opacity-80 transition"
                                    onclick="openPhotoModal('/Court-Reservation/<?= htmlspecialchars($user['gov_id_photo']) ?>', 'Government ID - <?= htmlspecialchars($user['gov_id_type'] ?? 'Unknown') ?>')">
                                <span class="absolute bottom-0 right-0 bg-blue-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center border-2 border-white shadow-md">ID</span>
                            </div>
                            <?php else: ?>
                            <div class="w-16 h-16 rounded border-2 border-gray-300 bg-gray-100 flex items-center justify-center text-xs text-gray-400">
                                <i class="fas fa-id-card text-lg"></i>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Face Photo -->
                            <?php if (!empty($user['face_photo'])): ?>
                            <div class="relative inline-block">
                                <img src="/Court-Reservation/<?= htmlspecialchars($user['face_photo']) ?>" alt="Face Photo" 
                                    class="w-16 h-16 rounded-full border-2 border-green-300 object-cover cursor-pointer hover:opacity-80 transition"
                                    onclick="openPhotoModal('/Court-Reservation/<?= htmlspecialchars($user['face_photo']) ?>', 'Face Photo')">
                                <span class="absolute bottom-0 right-0 bg-green-500 text-white text-sm font-bold rounded-full w-6 h-6 flex items-center justify-center border-2 border-white shadow-md">👤</span>
                            </div>
                            <?php else: ?>
                            <div class="w-16 h-16 rounded-full border-2 border-gray-300 bg-gray-100 flex items-center justify-center text-xs text-gray-400">
                                <i class="fas fa-user-slash text-lg"></i>
                            </div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-medium"><?= $user['booking_count'] ?? 0 ?></span>
                        <span class="text-gray-500 text-sm">bookings</span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <?= date('M d, Y', strtotime($user['created_at'])) ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php if (!empty($user['is_blacklisted']) && $user['is_blacklisted']): ?>
                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">Blacklisted</span>
                        <?php elseif (empty($user['is_active']) || !$user['is_active']): ?>
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">Inactive</span>
                        <?php else: ?>
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Active</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end space-x-2">
                            <?php if ($user['role'] !== 'admin'): ?>
                                <?php if (!empty($user['is_blacklisted']) && $user['is_blacklisted']): ?>
                                <form action="<?= url('admin/users/' . $user['id'] . '/unblacklist') ?>" method="POST" class="inline">
                                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                    <button type="submit" class="px-3 py-1 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition text-sm">
                                        <i class="fas fa-user-check mr-1"></i>Unblock
                                    </button>
                                </form>
                                <?php elseif (empty($user['is_active']) || !$user['is_active']): ?>
                                <form action="<?= url('admin/users/' . $user['id'] . '/activate') ?>" method="POST" class="inline">
                                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                    <button type="submit" class="px-3 py-1 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition text-sm">
                                        <i class="fas fa-check-circle mr-1"></i>Activate
                                    </button>
                                </form>
                                <?php else: ?>
                                <form action="<?= url('admin/users/' . $user['id'] . '/deactivate') ?>" method="POST" class="inline">
                                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                    <button type="submit" class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition text-sm">
                                        <i class="fas fa-user-times mr-1"></i>Deactivate
                                    </button>
                                </form>
                                <button onclick="openBlacklistModal(<?= $user['id'] ?>, '<?= htmlspecialchars($user['name']) ?>')" 
                                        class="px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition text-sm">
                                    <i class="fas fa-user-slash mr-1"></i>Blacklist
                                </button>
                                <?php endif; ?>
                                <button onclick="openDeleteModal(<?= $user['id'] ?>, '<?= htmlspecialchars($user['name']) ?>')" 
                                        class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div class="flex justify-center mt-8">
    <nav class="flex space-x-2">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>&<?= http_build_query(array_diff_key($_GET, ['page' => ''])) ?>" 
           class="px-4 py-2 rounded-lg <?= $currentPage == $i ? 'bg-ph-blue text-white' : 'bg-white text-gray-700 hover:bg-gray-100' ?>">
            <?= $i ?>
        </a>
        <?php endfor; ?>
    </nav>
</div>
<?php endif; ?>


<!-- Photo Modal -->
<div id="photoModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center p-4">
    <div class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full">
        <button onclick="closePhotoModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 z-10">
            <i class="fas fa-times text-2xl"></i>
        </button>
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4" id="photoTitle">Photo</h3>
            <img id="photoImage" src="" alt="Photo" class="w-full h-auto rounded-lg max-h-96 object-contain">
        </div>
    </div>
</div>

<!-- Blacklist Modal -->
<div id="blacklistModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold">Blacklist User</h3>
            <p class="text-sm text-gray-500" id="blacklistUserName"></p>
        </div>
        <form id="blacklistForm" method="POST">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            <div class="p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                <select name="reason" class="w-full border border-gray-300 rounded-lg px-4 py-2 mb-4">
                    <option value="no_show">No Show</option>
                    <option value="payment_fraud">Payment Fraud</option>
                    <option value="misconduct">Misconduct</option>
                    <option value="other">Other</option>
                </select>
                
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes (optional)</label>
                <textarea name="notes" rows="3" 
                          class="w-full border border-gray-300 rounded-lg px-4 py-2"
                          placeholder="Additional notes..."></textarea>
            </div>
            <div class="p-6 bg-gray-50 rounded-b-xl flex justify-end space-x-3">
                <button type="button" onclick="closeBlacklistModal()" 
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Blacklist User
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete User Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6 border-b border-red-200 bg-red-50">
            <h3 class="text-lg font-semibold text-red-900">Delete User</h3>
            <p class="text-sm text-red-700" id="deleteUserName"></p>
        </div>
        <form id="deleteForm" method="POST">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            <div class="p-6">
                <p class="text-gray-700 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                    This action cannot be undone. All user data, bookings, and photos will be permanently deleted.
                </p>
                <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-900 font-medium">Are you sure you want to delete this user?</p>
                </div>
            </div>
            <div class="p-6 bg-gray-50 rounded-b-xl flex justify-end space-x-3">
                <button type="button" onclick="closeDeleteModal()" 
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <i class="fas fa-trash mr-2"></i>Delete User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Photo Modal Functions
function openPhotoModal(photoUrl, title) {
    document.getElementById('photoImage').src = photoUrl;
    document.getElementById('photoTitle').textContent = title;
    document.getElementById('photoModal').classList.remove('hidden');
}

function closePhotoModal() {
    document.getElementById('photoModal').classList.add('hidden');
}

// Blacklist Modal Functions
function openBlacklistModal(userId, userName) {
    document.getElementById('blacklistForm').action = '<?= url('admin/users') ?>/' + userId + '/blacklist';
    document.getElementById('blacklistUserName').textContent = userName;
    document.getElementById('blacklistModal').classList.remove('hidden');
}

function closeBlacklistModal() {
    document.getElementById('blacklistModal').classList.add('hidden');
}

// Delete Modal Functions
function openDeleteModal(userId, userName) {
    document.getElementById('deleteForm').action = '<?= url('admin/users') ?>/' + userId + '/delete';
    document.getElementById('deleteUserName').textContent = userName;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}
</script>
