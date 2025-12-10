<?php
/**
 * Booking Controller
 */
class BookingController extends Controller {
    private $booking;
    private $court;
    private $payment;
    
    public function __construct() {
        parent::__construct();
        $this->booking = new Booking();
        $this->court = new Court();
        $this->payment = new Payment();
    }
    
    public function index() {
        $this->requireAuth();
        
        $page = $_GET['page'] ?? 1;
        $status = $_GET['status'] ?? '';
        
        $user = currentUser();
        $conditions = ['user_id' => $user['id']];
        if ($status) {
            $conditions['status'] = $status;
        }
        
        $bookings = $this->booking->paginate($page, 10, $conditions, 'booking_date', 'DESC');
        
        // Get full booking details with court info
        foreach ($bookings['items'] as &$booking) {
            $booking = $this->booking->findWithDetails($booking['id']);
        }
        
        $this->renderWithLayout('bookings.index', [
            'title' => 'My Bookings - ' . APP_NAME,
            'bookings' => $bookings,
            'currentStatus' => $status,
        ]);
    }
    
    public function create($courtId) {
        $this->requireAuth();
        
        $court = $this->court->findWithType($courtId);
        if (!$court) {
            $this->redirect('courts', 'Court not found.', 'error');
        }
        
        $user = currentUser();
        if (!empty($user['is_blacklisted'])) {
            $this->redirect("courts/{$courtId}", 'Your account is suspended and cannot make bookings.', 'error');
        }
        
        $date = $_GET['date'] ?? date('Y-m-d');
        $slots = $this->court->getAvailableSlots($courtId, $date);
        
        $this->renderWithLayout('bookings.create', [
            'title' => 'Book ' . $court['name'] . ' - ' . APP_NAME,
            'court' => $court,
            'slots' => $slots,
            'selectedDate' => $date,
        ]);
    }
    
    public function store() {
        $this->requireAuth();
        
        $errors = $this->validate($_POST, [
            'court_id' => 'required|numeric',
            'booking_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'payment_type' => 'required',
        ]);
        
        if ($errors) {
            flash('error', implode(' ', $errors));
            $this->back();
        }
        
        $courtId = (int)$_POST['court_id'];
        $date = $_POST['booking_date'];
        $startTime = $_POST['start_time'];
        $endTime = $_POST['end_time'];
        $isHalfCourt = isset($_POST['is_half_court']) && $_POST['is_half_court'] === '1';
        $paymentType = $_POST['payment_type']; // 'online' or 'venue'
        
        $court = $this->court->find($courtId);
        if (!$court) {
            $this->redirect('courts', 'Court not found.', 'error');
        }
        
        // Check availability again
        if (!$this->court->isAvailable($courtId, $date, $startTime, $endTime)) {
            flash('error', 'This time slot is no longer available.');
            $this->redirect("bookings/create/{$courtId}?date={$date}");
        }
        
        // Calculate price
        $price = $this->court->calculatePrice($courtId, $date, $startTime, $endTime, $isHalfCourt);
        
        // Create booking
        $user = currentUser();
        $bookingId = $this->booking->createBooking([
            'user_id' => $user['id'],
            'court_id' => $courtId,
            'booking_date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration_hours' => $price['hours'],
            'is_half_court' => $isHalfCourt ? 1 : 0,
            'hourly_rate' => $court['hourly_rate'],
            'total_amount' => $price['total'],
            'downpayment_amount' => $price['downpayment'],
            'balance_amount' => $price['balance'],
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_type' => $paymentType,
            'player_count' => $_POST['player_count'] ?? null,
            'notes' => sanitize($_POST['notes'] ?? ''),
        ]);
        
        // Log activity
        $log = new ActivityLog();
        $log->log('booking_created', 'New booking created', 'booking', $bookingId);
        
        // Create notification
        $notification = new Notification();
        $notification->createNotification(
            $user['id'],
            'booking_created',
            'Booking Created',
            "Your booking for {$court['name']} on {$date} has been created. Please complete payment.",
            ['booking_id' => $bookingId],
            'web'
        );
        
        // Redirect to payment
        $this->redirect("bookings/{$bookingId}/pay");
    }
    
    public function show($id) {
        $this->requireAuth();
        
        $booking = $this->booking->findWithDetails($id);
        if (!$booking) {
            $this->redirect('bookings', 'Booking not found.', 'error');
        }
        
        // Check ownership (unless admin)
        $user = currentUser();
        if ($booking['user_id'] !== $user['id'] && $user['role'] !== 'admin') {
            $this->redirect('bookings', 'Access denied.', 'error');
        }
        
        $payments = $this->payment->getByBooking($id);
        
        $this->renderWithLayout('bookings.show', [
            'title' => 'Booking #' . $booking['booking_code'] . ' - ' . APP_NAME,
            'booking' => $booking,
            'payments' => $payments,
        ]);
    }
    
    public function pay($id) {
        $this->requireAuth();
        
        $booking = $this->booking->findWithDetails($id);
        if (!$booking) {
            $this->redirect('bookings', 'Booking not found.', 'error');
        }
        
        $user = currentUser();
        if ($booking['user_id'] !== $user['id']) {
            $this->redirect('bookings', 'Access denied.', 'error');
        }
        
        if ($booking['payment_status'] === 'paid') {
            $this->redirect("bookings/{$id}", 'This booking is already paid.', 'info');
        }
        
        $this->renderWithLayout('bookings.pay', [
            'title' => 'Pay for Booking #' . $booking['booking_code'] . ' - ' . APP_NAME,
            'booking' => $booking,
        ]);
    }
    
    public function cancel($id) {
        $this->requireAuth();
        
        $booking = $this->booking->findWithDetails($id);
        if (!$booking) {
            $this->redirect('bookings', 'Booking not found.', 'error');
        }
        
        $user = currentUser();
        if ($booking['user_id'] !== $user['id'] && $user['role'] !== 'admin') {
            $this->redirect('bookings', 'Access denied.', 'error');
        }
        
        // Can only cancel pending or confirmed bookings
        if (!in_array($booking['status'], ['pending', 'confirmed'])) {
            $this->redirect("bookings/{$id}", 'This booking cannot be cancelled.', 'error');
        }
        
        $reason = sanitize($_POST['reason'] ?? 'Cancelled by user');
        $this->booking->cancel($id, $reason);
        
        // Log activity
        $log = new ActivityLog();
        $log->log('booking_cancelled', 'Booking cancelled: ' . $reason, 'booking', $id);
        
        // Notify user
        $notification = new Notification();
        $notification->createNotification(
            $booking['user_id'],
            'booking_cancelled',
            'Booking Cancelled',
            "Your booking #{$booking['booking_code']} has been cancelled.",
            ['booking_id' => $id],
            'web'
        );
        
        flash('success', 'Booking cancelled successfully.');
        $this->redirect('bookings');
    }
    
    public function confirmation($id) {
        $this->requireAuth();
        
        $booking = $this->booking->findWithDetails($id);
        if (!$booking) {
            $this->redirect('bookings', 'Booking not found.', 'error');
        }
        
        $user = currentUser();
        if ($booking['user_id'] !== $user['id']) {
            $this->redirect('bookings', 'Access denied.', 'error');
        }
        
        $this->renderWithLayout('bookings.confirmation', [
            'title' => 'Booking Confirmed - ' . APP_NAME,
            'booking' => $booking,
        ]);
    }
    
    // API: Verify entry QR code (for staff)
    public function verifyQR() {
        $qrCode = $_POST['qr_code'] ?? $_GET['qr'] ?? '';
        
        if (!$qrCode) {
            $this->json(['error' => 'QR code required'], 400);
        }
        
        $booking = $this->booking->verifyEntryQR($qrCode);
        
        if (!$booking) {
            $this->json([
                'success' => false,
                'message' => 'Invalid or expired QR code'
            ]);
        }
        
        $this->json([
            'success' => true,
            'message' => 'Valid booking',
            'booking' => [
                'code' => $booking['booking_code'],
                'court' => $booking['court_name'],
                'user' => $booking['user_name'],
                'time' => $booking['start_time'] . ' - ' . $booking['end_time'],
                'status' => $booking['status'],
            ]
        ]);
    }
    
    // Calendar view for a specific court
    public function calendar($courtId) {
        $court = $this->court->findWithType($courtId);
        if (!$court) {
            $this->redirect('courts', 'Court not found.', 'error');
        }
        
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');
        
        $calendarData = $this->booking->getCalendarData($courtId, $month, $year);
        
        $this->renderWithLayout('bookings.calendar', [
            'title' => $court['name'] . ' Schedule - ' . APP_NAME,
            'court' => $court,
            'calendarData' => $calendarData,
            'month' => $month,
            'year' => $year,
        ]);
    }
    
    public function review($id) {
        $this->requireAuth();
        
        $booking = $this->booking->findWithDetails($id);
        if (!$booking) {
            $this->redirect('bookings', 'Booking not found.', 'error');
        }
        
        $user = currentUser();
        if ($booking['user_id'] != $user['id']) {
            $this->redirect('bookings', 'Unauthorized.', 'error');
        }
        
        // Check if booking is completed
        if ($booking['status'] !== 'completed') {
            $this->redirect('bookings/' . $id, 'You can only review completed bookings.', 'error');
        }
        
        // Check if already reviewed
        $review = new Review();
        if ($review->hasReviewed($user['id'], $id)) {
            $this->redirect('bookings/' . $id, 'You have already reviewed this booking.', 'info');
        }
        
        $this->renderWithLayout('bookings.review', [
            'title' => 'Leave a Review - ' . APP_NAME,
            'booking' => $booking,
        ]);
    }
    
    public function storeReview($id) {
        $this->requireAuth();
        
        $booking = $this->booking->findWithDetails($id);
        if (!$booking) {
            $this->redirect('bookings', 'Booking not found.', 'error');
        }
        
        $user = currentUser();
        if ($booking['user_id'] != $user['id']) {
            $this->redirect('bookings', 'Unauthorized.', 'error');
        }
        
        $errors = $this->validate($_POST, [
            'rating' => 'required|numeric',
            'comment' => 'required|min:10|max:500',
        ]);
        
        if ($errors) {
            flash('error', implode(' ', $errors));
            $this->redirect('bookings/' . $id . '/review');
        }
        
        $review = new Review();
        
        // Check if already reviewed
        if ($review->hasReviewed($user['id'], $id)) {
            $this->redirect('bookings/' . $id, 'You have already reviewed this booking.', 'info');
        }
        
        $review->createReview([
            'court_id' => $booking['court_id'],
            'user_id' => $user['id'],
            'booking_id' => $id,
            'rating' => (int)$_POST['rating'],
            'comment' => sanitize($_POST['comment']),
        ]);
        
        flash('success', 'Thank you for your review!');
        $this->redirect('bookings/' . $id);
    }
}
