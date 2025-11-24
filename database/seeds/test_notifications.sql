-- Test notifications data
INSERT INTO notifications (user_id, type, title, message, related_user_id, is_read) VALUES
(1, 'match', 'New Match!', 'You have a new mutual match with Carelene Canque', 2, 0),
(1, 'message', 'New Message', 'Carelene Canque sent you a message', 2, 0),
(1, 'appointment', 'Appointment Confirmed', 'Your viewing appointment has been confirmed', NULL, 1),
(2, 'inquiry', 'New Inquiry', 'John Doe is interested in your Modern Studio Downtown', 1, 0);
