<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Multi-Tenant Subdomain Manager</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
        :root {
            --bg-color: #0b0f19;
            --card-bg: rgba(20, 26, 45, 0.6);
            --primary: #6366f1;
            --primary-glow: rgba(99, 102, 241, 0.15);
            --secondary: #10b981;
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            --border: rgba(255, 255, 255, 0.08);
            --card-hover-border: rgba(99, 102, 241, 0.4);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(16, 185, 129, 0.05) 0%, transparent 40%);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4rem;
            border-bottom: 1px solid var(--border);
            padding-bottom: 2rem;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #fff;
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
        }

        .logo-text h1 {
            font-size: 1.6rem;
            font-weight: 800;
            background: linear-gradient(to right, #ffffff, #a5b4fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .logo-text p {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .btn {
            background: linear-gradient(135deg, var(--primary), #4f46e5);
            color: white;
            border: none;
            padding: 0.8rem 1.6rem;
            border-radius: 10px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
            filter: brightness(1.1);
        }

        .btn:active {
            transform: translateY(0);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 2rem;
        }

        .card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 220px;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(800px circle at var(--mouse-x, 0) var(--mouse-y, 0), rgba(255, 255, 255, 0.04), transparent 40%);
            z-index: 1;
            pointer-events: none;
        }

        .card:hover {
            transform: translateY(-5px);
            border-color: var(--card-hover-border);
            box-shadow: 0 12px 30px rgba(99, 102, 241, 0.08);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
        }

        .badge {
            background: rgba(16, 185, 129, 0.1);
            color: var(--secondary);
            font-size: 0.75rem;
            padding: 0.3rem 0.7rem;
            border-radius: 9999px;
            font-weight: 600;
            border: 1px solid rgba(16, 185, 129, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .card-body {
            margin-bottom: 2rem;
        }

        .subdomain-link {
            font-size: 0.95rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
        }

        .subdomain-link:hover {
            color: #818cf8;
            text-decoration: underline;
        }

        .db-info {
            margin-top: 1rem;
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-size: 0.8rem;
            color: var(--text-muted);
            background: rgba(0, 0, 0, 0.15);
            padding: 0.8rem;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.03);
        }

        .db-info-item {
            display: flex;
            justify-content: space-between;
        }

        .db-info-label {
            font-weight: 500;
        }

        .db-info-value {
            font-family: monospace;
            color: #d1d5db;
        }

        .card-footer {
            display: flex;
            justify-content: flex-end;
        }

        /* Modal styling */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(5, 7, 13, 0.85);
            backdrop-filter: blur(8px);
            z-index: 100;
            display: none;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal {
            background: #0f1322;
            border: 1px solid var(--border);
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            padding: 2.5rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
            transform: scale(0.9);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
        }

        .modal.active {
            transform: scale(1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(to right, #fff, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .modal-close {
            color: var(--text-muted);
            font-size: 1.5rem;
            cursor: pointer;
            background: none;
            border: none;
            transition: color 0.2s;
        }

        .modal-close:hover {
            color: #fff;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            color: #d1d5db;
        }

        .form-input {
            width: 100%;
            background: rgba(0, 0, 0, 0.25);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.8rem 1rem;
            color: #fff;
            font-family: inherit;
            font-size: 0.95rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .subdomain-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .subdomain-suffix {
            position: absolute;
            right: 12px;
            color: var(--text-muted);
            font-size: 0.9rem;
            pointer-events: none;
        }

        .subdomain-input-wrapper .form-input {
            padding-right: 120px;
        }

        .loader-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 19, 34, 0.9);
            border-radius: 20px;
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 3px solid rgba(99, 102, 241, 0.1);
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 1rem;
        }

        .loader-text {
            font-weight: 600;
            font-size: 1rem;
            color: #fff;
        }

        .loader-subtext {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 4rem 2rem;
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px dashed var(--border);
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--primary);
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: none;
            font-size: 0.9rem;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #fca5a5;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #a7f3d0;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo-area">
                <div class="logo-icon">
                    <i class="fa-solid fa-server"></i>
                </div>
                <div class="logo-text">
                    <h1>Tenant Manager</h1>
                    <p>Subdomain-Based Multi-Tenancy Portal</p>
                </div>
            </div>
            <button class="btn" id="openCreateModalBtn">
                <i class="fa-solid fa-plus"></i> Register Client
            </button>
        </header>

        <main>
            <div class="grid" id="clientsGrid">
                @forelse($clients as $client)
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ $client->name }}</h3>
                            <span class="badge">
                                <i class="fa-solid fa-circle-check"></i> Active
                            </span>
                        </div>
                        <div class="card-body">
                            <a href="http://{{ $client->subdomain }}.localhost{{ request()->getPort() ? ':'.request()->getPort() : '' }}/dashboard" target="_blank" class="subdomain-link">
                                http://{{ $client->subdomain }}.localhost{{ request()->getPort() ? ':'.request()->getPort() : '' }} <i class="fa-solid fa-up-right-from-square" style="font-size: 0.8rem;"></i>
                            </a>
                            <div class="db-info">
                                <div class="db-info-item">
                                    <span class="db-info-label">DB Host</span>
                                    <span class="db-info-value">{{ $client->db_host }}</span>
                                </div>
                                <div class="db-info-item">
                                    <span class="db-info-label">DB Name</span>
                                    <span class="db-info-value">{{ $client->db_name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="http://{{ $client->subdomain }}.localhost{{ request()->getPort() ? ':'.request()->getPort() : '' }}/dashboard" target="_blank" class="btn" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                                Visit Dashboard
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fa-solid fa-database"></i>
                        <h2>No clients registered yet</h2>
                        <p style="margin-top: 0.5rem;">Click "Register Client" above to create your first client subdomain and database.</p>
                    </div>
                @endforelse
            </div>
        </main>
    </div>

    <!-- Create Client Modal -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal" id="createModal">
            <!-- Loader Overlay -->
            <div class="loader-overlay" id="loaderOverlay">
                <div class="spinner"></div>
                <div class="loader-text">Creating Isolated Database...</div>
                <div class="loader-subtext">Running tenant migrations & configuring domain...</div>
            </div>

            <div class="modal-header">
                <h2 class="modal-title">Register Client</h2>
                <button class="modal-close" id="closeModalBtn">&times;</button>
            </div>

            <div class="alert alert-error" id="modalError"></div>
            <div class="alert alert-success" id="modalSuccess"></div>

            <form id="createClientForm">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="clientName">Client Name</label>
                    <input class="form-input" type="text" id="clientName" name="name" placeholder="e.g. Acme Corp" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="clientSubdomain">Subdomain</label>
                    <div class="subdomain-input-wrapper">
                        <input class="form-input" type="text" id="clientSubdomain" name="subdomain" placeholder="e.g. acme" required>
                        <span class="subdomain-suffix">.localhost</span>
                    </div>
                </div>

                <button class="btn" type="submit" style="width: 100%; justify-content: center; margin-top: 1rem;">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Create Tenant Platform
                </button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Modal Open
            $('#openCreateModalBtn').on('click', function() {
                $('#modalError').hide();
                $('#modalSuccess').hide();
                $('#createClientForm')[0].reset();
                $('#modalOverlay').css('display', 'flex').animate({ opacity: 1 }, 200, function() {
                    $('#createModal').addClass('active');
                });
            });

            // Modal Close
            function closeModal() {
                $('#createModal').removeClass('active');
                setTimeout(function() {
                    $('#modalOverlay').animate({ opacity: 0 }, 200, function() {
                        $(this).hide();
                    });
                }, 150);
            }

            $('#closeModalBtn, #modalOverlay').on('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });

            // Form Submit via AJAX
            $('#createClientForm').on('submit', function(e) {
                e.preventDefault();
                $('#modalError').hide();
                $('#modalSuccess').hide();
                $('#loaderOverlay').css('display', 'flex');

                const formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('main.clients.store') }}",
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    success: function(response) {
                        $('#loaderOverlay').hide();
                        if (response.success) {
                            $('#modalSuccess').text(response.message).fadeIn();
                            
                            // Dynamically append new card
                            const newCard = `
                                <div class="card" style="opacity: 0; transform: translateY(20px);">
                                    <div class="card-header">
                                        <h3 class="card-title">${$('#clientName').val()}</h3>
                                        <span class="badge">
                                            <i class="fa-solid fa-circle-check"></i> Active
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <a href="${response.redirect_url}" target="_blank" class="subdomain-link">
                                            ${response.redirect_url.replace('/dashboard', '')} <i class="fa-solid fa-up-right-from-square" style="font-size: 0.8rem;"></i>
                                        </a>
                                        <div class="db-info">
                                            <div class="db-info-item">
                                                <span class="db-info-label">DB Host</span>
                                                <span class="db-info-value">127.0.0.1</span>
                                            </div>
                                            <div class="db-info-item">
                                                <span class="db-info-label">DB Name</span>
                                                <span class="db-info-value">sub_domain_${response.subdomain.replace(/-/g, '_')}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <a href="${response.redirect_url}" target="_blank" class="btn" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                                            Visit Dashboard
                                        </a>
                                    </div>
                                </div>
                            `;

                            // Remove empty state if present
                            $('.empty-state').remove();

                            const $newCardHtml = $(newCard);
                            $('#clientsGrid').prepend($newCardHtml);
                            $newCardHtml.animate({ opacity: 1 }, 400).css('transform', 'translateY(0)');

                            setTimeout(function() {
                                closeModal();
                            }, 1500);
                        }
                    },
                    error: function(xhr) {
                        $('#loaderOverlay').hide();
                        let errorMsg = 'An error occurred while creating the tenant.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            // Extract validation errors
                            errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        }
                        $('#modalError').html(errorMsg).fadeIn();
                    }
                });
            });

            // Card mouse hover effect (glowing overlay matching mouse coordinates)
            $(document).on('mousemove', '.card', function(e) {
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                this.style.setProperty('--mouse-x', `${x}px`);
                this.style.setProperty('--mouse-y', `${y}px`);
            });
        });
    </script>
</body>
</html>
