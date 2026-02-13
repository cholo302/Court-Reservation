<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <a href="<?= url('/') ?>" class="inline-flex items-center">
                <i class="fas fa-basketball-ball text-ph-blue text-3xl mr-2"></i>
                <span class="text-2xl font-bold text-ph-blue"><?= APP_NAME ?></span>
            </a>
            <h2 class="mt-6 text-3xl font-bold text-gray-900">Create Account</h2>
            <p class="mt-2 text-gray-600">Join and start booking courts</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-8">
            <form action="<?= url('register') ?>" method="POST" enctype="multipart/form-data" class="space-y-5">
                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text" id="name" name="name" value="<?= old('name') ?>" required
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                            placeholder="Juan Dela Cruz">
                    </div>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email" value="<?= old('email') ?>" required
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                            placeholder="juan@example.com">
                    </div>
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-phone text-gray-400"></i>
                        </div>
                        <input type="tel" id="phone" name="phone" value="<?= old('phone') ?>" required
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                            placeholder="09171234567">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Philippine mobile number format</p>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password" required minlength="6"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                            placeholder="••••••••">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">At least 6 characters</p>
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent"
                            placeholder="••••••••">
                    </div>
                </div>
                
                <!-- Government ID Verification Section -->
                <div class="border-t pt-5 mt-5">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Government ID Verification</h3>
                    
                    <div>
                        <label for="gov_id_type" class="block text-sm font-medium text-gray-700 mb-1">ID Type</label>
                        <select id="gov_id_type" name="gov_id_type" required
                            class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent">
                            <option value="">-- Select ID Type --</option>
                            <option value="lto_license" <?= old('gov_id_type') === 'lto_license' ? 'selected' : '' ?>>LTO Driver's License</option>
                            <option value="passport" <?= old('gov_id_type') === 'passport' ? 'selected' : '' ?>>Philippine Passport</option>
                            <option value="nbi" <?= old('gov_id_type') === 'nbi' ? 'selected' : '' ?>>NBI Clearance</option>
                            <option value="national_id" <?= old('gov_id_type') === 'national_id' ? 'selected' : '' ?>>National ID</option>
                            <option value="barangay_id" <?= old('gov_id_type') === 'barangay_id' ? 'selected' : '' ?>>Barangay ID</option>
                            <option value="sss_id" <?= old('gov_id_type') === 'sss_id' ? 'selected' : '' ?>>SSS ID</option>
                            <option value="tin_id" <?= old('gov_id_type') === 'tin_id' ? 'selected' : '' ?>>TIN ID</option>
                            <option value="prc_id" <?= old('gov_id_type') === 'prc_id' ? 'selected' : '' ?>>PRC License</option>
                            <option value="postal_id" <?= old('gov_id_type') === 'postal_id' ? 'selected' : '' ?>>Postal ID</option>
                        </select>
                    </div>
                    
                    <!-- ID Card Photo Upload -->
                    <div class="mt-3">
                        <label for="gov_id_photo" class="block text-sm font-medium text-gray-700 mb-2">Upload ID Card Photo <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="file" id="gov_id_photo" name="gov_id_photo" accept="image/jpeg,image/png,image/gif,image/webp" required
                                class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent cursor-pointer"
                                onchange="previewImage(event, 'gov_id')">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">JPG, PNG, GIF or WebP • Max 5MB • Clear photo of your ID</p>
                        
                        <!-- ID Card Preview -->
                        <div id="govIdPreview" class="mt-3 hidden">
                            <img id="govIdPreviewImage" src="" alt="ID Preview" class="w-full h-auto rounded-lg border border-gray-300 max-h-40">
                            <button type="button" onclick="clearImage('gov_id')" class="mt-2 text-xs text-red-600 hover:text-red-800 font-medium">
                                <i class="fas fa-trash mr-1"></i> Remove Photo
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Face Photo Section -->
                <div class="border-t pt-5 mt-5">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Face Photo (Profile)</h3>
                    
                    <!-- Face Photo Upload -->
                    <div>
                        <label for="face_photo" class="block text-sm font-medium text-gray-700 mb-2">Upload Face Photo <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="file" id="face_photo" name="face_photo" accept="image/jpeg,image/png,image/gif,image/webp" required
                                class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ph-blue focus:border-transparent cursor-pointer"
                                onchange="previewImage(event, 'face')">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">JPG, PNG, GIF or WebP • Max 5MB • Clear frontal face photo</p>
                        
                        <!-- Face Photo Preview -->
                        <div id="facePreview" class="mt-3 hidden">
                            <img id="facePreviewImage" src="" alt="Face Preview" class="w-full h-auto rounded-lg border border-gray-300 max-h-40">
                            <button type="button" onclick="clearImage('face')" class="mt-2 text-xs text-red-600 hover:text-red-800 font-medium">
                                <i class="fas fa-trash mr-1"></i> Remove Photo
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <input type="checkbox" id="terms" name="terms" required
                        class="h-4 w-4 mt-1 text-ph-blue focus:ring-ph-blue border-gray-300 rounded">
                    <label for="terms" class="ml-2 text-sm text-gray-600">
                        I agree to the <a href="<?= url('terms') ?>" class="text-ph-blue hover:underline">Terms of Service</a> 
                        and <a href="<?= url('privacy') ?>" class="text-ph-blue hover:underline">Privacy Policy</a>
                    </label>
                </div>
                
                <button type="submit" class="w-full bg-ph-blue text-white py-3 rounded-lg font-semibold hover:bg-blue-800 transition">
                    <i class="fas fa-user-plus mr-2"></i> Create Account
                </button>
            </form>
            
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Or sign up with</span>
                    </div>
                </div>
                
                <div class="mt-6 grid grid-cols-2 gap-3">
                    <a href="<?= url('auth/facebook') ?>" class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        <i class="fab fa-facebook text-blue-600 text-xl mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Facebook</span>
                    </a>
                    <a href="<?= url('auth/google') ?>" class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        <i class="fab fa-google text-red-500 text-xl mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Google</span>
                    </a>
                </div>
            </div>
        </div>
        
        <p class="mt-6 text-center text-gray-600">
            Already have an account? 
            <a href="<?= url('login') ?>" class="text-ph-blue font-semibold hover:text-blue-800">Sign in</a>
        </p>
    </div>
</div>

<script>
function previewImage(event, type) {
    const file = event.target.files[0];
    
    let preview, previewImg;
    if (type === 'gov_id') {
        preview = document.getElementById('govIdPreview');
        previewImg = document.getElementById('govIdPreviewImage');
    } else if (type === 'face') {
        preview = document.getElementById('facePreview');
        previewImg = document.getElementById('facePreviewImage');
    }
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('hidden');
    }
}

function clearImage(type) {
    let inputId, previewId;
    if (type === 'gov_id') {
        inputId = 'gov_id_photo';
        previewId = 'govIdPreview';
    } else if (type === 'face') {
        inputId = 'face_photo';
        previewId = 'facePreview';
    }
    
    document.getElementById(inputId).value = '';
    document.getElementById(previewId).classList.add('hidden');
}
</script>
