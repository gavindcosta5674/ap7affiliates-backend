<?php
// ====================================================
// /api/media.php
// GET    - List all media files
// POST   - Upload new media (multipart form)
// DELETE - Delete media (?id=)
// ====================================================
require_once __DIR__ . '/config.php';

$method = $_SERVER['REQUEST_METHOD'];

// Ensure uploads directory exists
$uploadsDir = __DIR__ . '/../uploads';
if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0755, true);
}

// ── GET: List all media ─────────────────────────────────────────────
if ($method === 'GET') {
    try {
        $pdo = getDB();
        $stmt = $pdo->query("SELECT * FROM media_gallery ORDER BY upload_date DESC");
        $media = $stmt->fetchAll();
        echo json_encode($media);
    } catch (Exception $e) {
        // If table doesn't exist, return empty
        echo json_encode([]);
    }
    exit;
}

// ── POST: Upload media ──────────────────────────────────────────────
if ($method === 'POST') {
    try {
        $pdo = getDB();
        
        $title = $_POST['title'] ?? 'Untitled';
        $mediaType = $_POST['media_type'] ?? 'image';
        
        // Allowed types
        $allowedTypes = ['image', 'banner', 'video', 'pdf'];
        if (!in_array($mediaType, $allowedTypes)) {
            $mediaType = 'image';
        }

        // Handle file upload
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['error' => 'File upload failed']);
            exit;
        }

        $file = $_FILES['file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Validate extension based on type
        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $videoExts = ['mp4', 'avi', 'mov', 'wmv'];
        $pdfExts = ['pdf'];

        $valid = false;
        if ($mediaType === 'image' || $mediaType === 'banner') {
            $valid = in_array($ext, $imageExts);
        } elseif ($mediaType === 'video') {
            $valid = in_array($ext, $videoExts);
        } elseif ($mediaType === 'pdf') {
            $valid = in_array($ext, $pdfExts);
        }

        if (!$valid) {
            http_response_code(400);
            echo json_encode(['error' => "Invalid file extension for type: $mediaType"]);
            exit;
        }

        // Generate unique filename
        $uniqueName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file['name']);
        $destPath = $uploadsDir . '/' . $uniqueName;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save file']);
            exit;
        }

        // Save to database
        $stmt = $pdo->prepare("INSERT INTO media_gallery (title, media_type, file_name, file_path) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $mediaType, $file['name'], 'uploads/' . $uniqueName]);

        $newId = $pdo->lastInsertId();
        echo json_encode([
            'id' => $newId,
            'title' => $title,
            'media_type' => $mediaType,
            'file_name' => $file['name'],
            'file_path' => 'uploads/' . $uniqueName,
            'upload_date' => date('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// ── DELETE: Remove media ────────────────────────────────────────────
if ($method === 'DELETE') {
    try {
        $pdo = getDB();
        $id = $_GET['id'] ?? '';

        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Media ID required']);
            exit;
        }

        // Get file info before deleting
        $stmt = $pdo->prepare("SELECT file_path FROM media_gallery WHERE id = ?");
        $stmt->execute([$id]);
        $media = $stmt->fetch();

        if ($media) {
            $filePath = __DIR__ . '/../' . $media['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $stmt = $pdo->prepare("DELETE FROM media_gallery WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['success' => true, 'message' => 'Media deleted']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);