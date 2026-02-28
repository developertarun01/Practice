<?php
// Public professional profile page - No authentication required
require_once 'includes/config.php';

// Get the professional slug from URL
$slug = isset($_GET['slug']) ? esc($_GET['slug']) : '';

if (empty($slug)) {
    die('Professional not found');
}

// Fetch professional details by slug
$prof_result = $conn->query("
    SELECT * FROM professionals 
    WHERE professional_slug = '$slug' 
    LIMIT 1
");

if ($prof_result->num_rows == 0) {
    die('Professional profile not found or not available');
}

$professional = $prof_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($professional['name']); ?> - Servon Professional Profile</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }

        .profile-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }

        .profile-image {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 4px solid #007bff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .profile-header h1 {
            font-size: 32px;
            margin: 15px 0 5px 0;
            color: #1a1a1a;
        }

        .profile-header .service-badge {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .profile-header .rating {
            font-size: 20px;
            color: #ffc107;
            margin: 10px 0;
        }

        .profile-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 30px 0;
        }

        .detail-item {
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 6px;
            border-left: 4px solid #007bff;
        }

        .detail-item strong {
            display: block;
            color: #007bff;
            margin-bottom: 5px;
            font-size: 12px;
            text-transform: uppercase;
        }

        .detail-item p {
            margin: 0;
            font-size: 16px;
            color: #333;
        }

        .contact-section {
            background-color: #f0f9ff;
            padding: 25px;
            border-radius: 6px;
            margin: 30px 0;
            text-align: center;
        }

        .contact-section h2 {
            margin-top: 0;
            color: #007bff;
        }

        .contact-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .contact-btn {
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .contact-btn.phone {
            background-color: #28a745;
            color: white;
        }

        .contact-btn.phone:hover {
            background-color: #218838;
        }

        .contact-btn.email {
            background-color: #17a2b8;
            color: white;
        }

        .contact-btn.email:hover {
            background-color: #138496;
        }

        .documents-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        }

        .documents-section h3 {
            color: #1a1a1a;
            margin-bottom: 15px;
        }

        .document-item {
            display: flex;
            align-items: center;
            padding: 12px;
            background-color: #f9f9f9;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .document-item a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .document-item a:hover {
            text-decoration: underline;
        }

        .footer-text {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
            color: #666;
            font-size: 13px;
        }

        .logo {
            display: block;
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }

        @media (max-width: 600px) {
            .profile-container {
                margin: 20px;
                padding: 20px;
            }

            .profile-header h1 {
                font-size: 24px;
            }

            .profile-details {
                grid-template-columns: 1fr;
            }

            .contact-buttons {
                flex-direction: column;
            }

            .contact-btn {
                width: 100%;
            }
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-badge.verified {
            background-color: #d1fae5;
            color: #047857;
        }

        .status-badge.active {
            background-color: #dcfce7;
            color: #166534;
        }
    </style>
</head>

<body>
    <div class="profile-container">
        <div class="logo">✓ Servon</div>

        <div class="profile-header">
            <?php if (!empty($professional['staff_image'])): ?>
                <img src="<?php echo htmlspecialchars($professional['staff_image']); ?>" alt="<?php echo htmlspecialchars($professional['name']); ?>" class="profile-image">
            <?php else: ?>
                <div style="width: 200px; height: 200px; border-radius: 50%; background-color: #e0e0e0; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; font-size: 50px; border: 4px solid #007bff;">👤</div>
            <?php endif; ?>

            <h1><?php echo htmlspecialchars($professional['name']); ?></h1>
            <span class="service-badge"><?php echo htmlspecialchars($professional['service']); ?></span>

            <div class="rating">
                <?php
                $rating = floatval($professional['rating']);
                for ($i = 0; $i < 5; $i++) {
                    if ($i < $rating) {
                        echo '⭐';
                    } else {
                        echo '☆';
                    }
                }
                ?>
                <span style="color: #666; font-size: 14px; margin-left: 10px;"><?php echo $professional['rating']; ?>/5.0</span>
            </div>

            <div style="margin-top: 15px;">
                <span class="status-badge verified">✓ Verified Professional</span>
                <span class="status-badge active" style="margin-left: 10px;">🟢 Available</span>
            </div>
        </div>

        <div class="profile-details">
            <div class="detail-item">
                <strong>Experience</strong>
                <p><?php echo intval($professional['experience']); ?> years</p>
            </div>
            <div class="detail-item">
                <strong>Hours Per Day</strong>
                <p><?php echo intval($professional['hours']); ?> hours</p>
            </div>
            <div class="detail-item">
                <strong>Gender</strong>
                <p><?php echo htmlspecialchars($professional['gender']); ?></p>
            </div>
            <div class="detail-item">
                <strong>Location</strong>
                <p><?php echo !empty($professional['location']) ? htmlspecialchars($professional['location']) : 'Available'; ?></p>
            </div>
            <?php if (!empty($professional['language'])): ?>
                <div class="detail-item">
                    <strong>Languages</strong>
                    <p><?php echo htmlspecialchars($professional['language']); ?></p>
                </div>
            <?php endif; ?>
            <?php if (!empty($professional['food_type'])): ?>
                <div class="detail-item">
                    <strong>Food Type</strong>
                    <p><?php echo htmlspecialchars($professional['food_type']); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <div class="footer-text">
            <p>This is a verified professional profile on Servon. All professionals are thoroughly vetted and verified.</p>
            <p style="margin-top: 15px; color: #999;">© <?php echo date('Y'); ?> Servon - Domestic Support Solution. All rights reserved.</p>
        </div>
    </div>
</body>

</html>