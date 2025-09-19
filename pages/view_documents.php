<?php
// Simple, secure document browser for admin to view applicant uploads
// Usage: view_documents.php?nic=PASSPORT_OR_NIC

// Config
// uploads/documents is at project root; this file lives in /pages, so go one level up
$root = realpath(__DIR__ . '/../uploads/documents');
if ($root === false) {
    http_response_code(500);
    echo 'Uploads directory not found.';
    exit;
}

// Get NIC/passport (can include spaces or symbols as stored by uploader)
$nic = isset($_GET['nic']) ? trim($_GET['nic']) : '';
if ($nic === '') {
    http_response_code(400);
    echo 'Missing NIC/Passport parameter.';
    exit;
}

// Resolve path and ensure it stays under root
// Try direct path (safe, enforced under root)
$candidate = realpath($root . DIRECTORY_SEPARATOR . $nic);
$dir = ($candidate && strpos($candidate, $root) === 0 && is_dir($candidate)) ? $candidate : '';

// If not found, try case-insensitive single-level match, and also normalized name match
if ($dir === '') {
    $targetNorm = strtolower(preg_replace('/[^a-z0-9]/i', '', $nic));
    foreach (glob($root . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) as $candidate) {
        $base = basename($candidate);
        if (strcasecmp($base, $nic) === 0) {
            $dir = realpath($candidate);
            break;
        }
        $candNorm = strtolower(preg_replace('/[^a-z0-9]/i', '', $base));
        if ($candNorm === $targetNorm) {
            $dir = realpath($candidate);
            break;
        }
    }
}

if (!$dir) {
    http_response_code(404);
    echo 'No documents found for ' . htmlspecialchars($nic);
    exit;
}

$files = array_values(array_filter(scandir($dir), function ($f) use ($dir) {
    return $f !== '.' && $f !== '..' && is_file($dir . DIRECTORY_SEPARATOR . $f);
}));

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Documents for <?php echo htmlspecialchars($nic); ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 0;
            margin: 0;
        }

        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .header-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .applicant-name {
            color: #667eea;
            font-weight: 600;
        }

        .close-btn {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            border: none;
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .close-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
            color: white;
            text-decoration: none;
        }

        .documents-container {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .documents-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f7fafc;
        }

        .documents-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2d3748;
            margin: 0;
        }

        .files-count {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .file-grid {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        }

        .file-card {
            background: #fafbfc;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 24px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .file-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border-color: #667eea;
        }

        .file-icon-container {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            font-size: 1.8rem;
            color: white;
            font-weight: 600;
        }

        .file-icon-pdf {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
        }

        .file-icon-image {
            background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
        }

        .file-icon-doc {
            background: linear-gradient(135deg, #4dabf7 0%, #1971c2 100%);
        }

        .file-icon-default {
            background: linear-gradient(135deg, #868e96 0%, #495057 100%);
        }

        .file-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            line-height: 1.4;
            word-break: break-word;
        }

        .file-meta {
            color: #718096;
            font-size: 0.875rem;
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .file-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-view {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            color: white;
            text-decoration: none;
        }

        .btn-download {
            background: #f7fafc;
            color: #4a5568;
            border: 2px solid #e2e8f0;
        }

        .btn-download:hover {
            background: #edf2f7;
            border-color: #cbd5e0;
            color: #2d3748;
            text-decoration: none;
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #718096;
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #4a5568;
        }

        .empty-description {
            font-size: 1rem;
            opacity: 0.8;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 20px 15px;
            }

            .header-card,
            .documents-container {
                padding: 20px;
            }

            .header-title {
                font-size: 1.5rem;
            }

            .file-grid {
                grid-template-columns: 1fr;
            }

            .documents-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
        }

        .search-container {
            margin-bottom: 25px;
        }

        .search-box {
            position: relative;
            max-width: 400px;
        }

        .search-input {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: #fafbfc;
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #718096;
            font-size: 0.9rem;
        }

        .search-clear {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #718096;
            cursor: pointer;
            padding: 8px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .search-clear:hover {
            background: #edf2f7;
            color: #4a5568;
        }

        .file-card.hidden {
            display: none;
        }

        .no-results {
            text-align: center;
            padding: 40px 20px;
            color: #718096;
            display: none;
        }

        .no-results.show {
            display: block;
        }

        .stats-bar {
            background: #f7fafc;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.875rem;
            color: #4a5568;
        }

        .stat-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }
    </style>
</head>

<body>
    <div class="main-container">
        <div class="header-card">
            <div class="d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <div class="header-icon">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <div>
                        Documents for <span class="applicant-name"><?php echo htmlspecialchars($nic); ?></span>
                    </div>
                </div>
                <a class="close-btn" href="javascript:window.close()">
                    <i class="fas fa-times"></i> Close
                </a>
            </div>
        </div>

        <div class="documents-container">
            <?php if (empty($files)) : ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-file-excel"></i>
                    </div>
                    <div class="empty-title">No Documents Found</div>
                    <div class="empty-description">No files have been uploaded for this applicant yet.</div>
                </div>
            <?php else: ?>
                <div class="documents-header">
                    <h5 class="documents-title">Uploaded Documents</h5>
                    <div class="d-flex align-items-center gap-3">
                        <span class="files-count"><?php echo count($files); ?> file<?php echo count($files) !== 1 ? 's' : ''; ?></span>
                    </div>
                </div>

                <?php if (count($files) > 3): ?>
                    <div class="search-container mb-4">
                        <div class="search-box">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" id="fileSearch" class="search-input" placeholder="Search documents..." onkeyup="filterFiles()">
                            <button class="search-clear" onclick="clearSearch()" style="display: none;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="file-grid">
                    <?php foreach ($files as $f):
                        $path = $dir . DIRECTORY_SEPARATOR . $f;
                        $size = filesize($path);
                        $mtime = date('M j, Y \a\t g:i A', filemtime($path));
                        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));

                        // Determine file type and icon
                        $fileType = 'default';
                        $fileIcon = 'fas fa-file';
                        if (in_array($ext, array('pdf'))) {
                            $fileType = 'pdf';
                            $fileIcon = 'fas fa-file-pdf';
                        } elseif (in_array($ext, array('png', 'jpg', 'jpeg', 'gif', 'webp'))) {
                            $fileType = 'image';
                            $fileIcon = 'fas fa-file-image';
                        } elseif (in_array($ext, array('doc', 'docx'))) {
                            $fileType = 'doc';
                            $fileIcon = 'fas fa-file-word';
                        }

                        // Format file size
                        if ($size < 1024) {
                            $sizeFormatted = $size . ' B';
                        } elseif ($size < 1024 * 1024) {
                            $sizeFormatted = number_format($size / 1024, 1) . ' KB';
                        } else {
                            $sizeFormatted = number_format($size / (1024 * 1024), 1) . ' MB';
                        }
                    ?>
                        <div class="file-card">
                            <div class="file-icon-container file-icon-<?php echo $fileType; ?>">
                                <i class="<?php echo $fileIcon; ?>"></i>
                            </div>

                            <div class="file-name"><?php echo htmlspecialchars($f); ?></div>

                            <div class="file-meta">
                                <div class="meta-item">
                                    <i class="fas fa-hdd"></i>
                                    <span><?php echo $sizeFormatted; ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span><?php echo htmlspecialchars($mtime); ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-tag"></i>
                                    <span><?php echo strtoupper($ext); ?></span>
                                </div>
                            </div>

                            <div class="file-actions">
                                <a class="btn-action btn-view" target="_blank" href="serve_document.php?nic=<?php echo urlencode($nic); ?>&file=<?php echo urlencode($f); ?>">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a class="btn-action btn-download" target="_blank" href="serve_document.php?nic=<?php echo urlencode($nic); ?>&file=<?php echo urlencode($f); ?>&download=1">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="no-results" id="noResults">
                    <div style="font-size: 2rem; margin-bottom: 15px; opacity: 0.5;">
                        <i class="fas fa-search"></i>
                    </div>
                    <div style="font-size: 1.1rem; font-weight: 600; margin-bottom: 5px;">No matching files</div>
                    <div style="opacity: 0.8;">Try adjusting your search terms</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function filterFiles() {
            const searchInput = document.getElementById('fileSearch');
            const searchTerm = searchInput.value.toLowerCase();
            const fileCards = document.querySelectorAll('.file-card');
            const noResults = document.getElementById('noResults');
            const clearBtn = document.querySelector('.search-clear');

            let visibleCount = 0;

            fileCards.forEach(card => {
                const fileName = card.querySelector('.file-name').textContent.toLowerCase();
                const fileExt = card.querySelector('.meta-item:last-child span').textContent.toLowerCase();

                if (fileName.includes(searchTerm) || fileExt.includes(searchTerm)) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            // Show/hide no results message
            if (visibleCount === 0 && searchTerm !== '') {
                noResults.classList.add('show');
            } else {
                noResults.classList.remove('show');
            }

            // Show/hide clear button
            if (searchTerm !== '') {
                clearBtn.style.display = 'block';
            } else {
                clearBtn.style.display = 'none';
            }
        }

        function clearSearch() {
            const searchInput = document.getElementById('fileSearch');
            const fileCards = document.querySelectorAll('.file-card');
            const noResults = document.getElementById('noResults');
            const clearBtn = document.querySelector('.search-clear');

            searchInput.value = '';

            fileCards.forEach(card => {
                card.classList.remove('hidden');
            });

            noResults.classList.remove('show');
            clearBtn.style.display = 'none';
        }

        // Add keyboard shortcut for search
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                const searchInput = document.getElementById('fileSearch');
                if (searchInput) {
                    searchInput.focus();
                }
            }

            if (e.key === 'Escape') {
                clearSearch();
            }
        });

        // Add file type statistics
        document.addEventListener('DOMContentLoaded', function() {
            const fileCards = document.querySelectorAll('.file-card');
            const stats = {};
            let totalSize = 0;

            fileCards.forEach(card => {
                const ext = card.querySelector('.meta-item:last-child span').textContent.toLowerCase();
                const sizeText = card.querySelector('.meta-item:first-child span').textContent;

                // Count file types
                stats[ext] = (stats[ext] || 0) + 1;

                // Calculate total size (rough estimation)
                const sizeMatch = sizeText.match(/([0-9.]+)\s*(KB|MB)/);
                if (sizeMatch) {
                    const size = parseFloat(sizeMatch[1]);
                    const unit = sizeMatch[2];
                    totalSize += unit === 'MB' ? size * 1024 : size;
                }
            });

            // Add stats bar if there are multiple files
            if (fileCards.length > 1) {
                const statsBar = document.createElement('div');
                statsBar.className = 'stats-bar';

                let statsHTML = '<div style="display: flex; gap: 20px; flex-wrap: wrap;">';

                // Total size
                const totalSizeFormatted = totalSize > 1024 ?
                    (totalSize / 1024).toFixed(1) + ' MB' :
                    totalSize.toFixed(1) + ' KB';

                statsHTML += `
                    <div class="stat-item">
                        <div class="stat-icon" style="background: #e6f3ff; color: #0066cc;">
                            <i class="fas fa-hdd"></i>
                        </div>
                        <span>Total: ${totalSizeFormatted}</span>
                    </div>
                `;

                // File types
                Object.entries(stats).forEach(([ext, count]) => {
                    if (count > 1) {
                        statsHTML += `
                            <div class="stat-item">
                                <div class="stat-icon" style="background: #f0f9ff; color: #0369a1;">
                                    <i class="fas fa-file"></i>
                                </div>
                                <span>${count} ${ext.toUpperCase()} files</span>
                            </div>
                        `;
                    }
                });

                statsHTML += '</div>';
                statsBar.innerHTML = statsHTML;

                const fileGrid = document.querySelector('.file-grid');
                fileGrid.parentNode.insertBefore(statsBar, fileGrid);
            }
        });
    </script>
</body>

</html>