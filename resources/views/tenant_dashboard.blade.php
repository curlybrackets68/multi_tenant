<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - {{ $client->name }}</title>
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
            --bg-color: #080b11;
            --card-bg: rgba(17, 22, 37, 0.75);
            --primary: #06b6d4; /* Tenant Accent: Cyan */
            --primary-glow: rgba(6, 182, 212, 0.15);
            --success: #10b981;
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            --border: rgba(255, 255, 255, 0.08);
            --table-hover: rgba(255, 255, 255, 0.02);
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
            background-image: 
                radial-gradient(circle at 5% 5%, rgba(6, 182, 212, 0.05) 0%, transparent 35%),
                radial-gradient(circle at 95% 95%, rgba(16, 185, 129, 0.03) 0%, transparent 35%);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2.5rem 2rem;
        }

        /* Top Nav */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border);
            padding: 1.25rem 2rem;
            border-radius: 16px;
        }

        .tenant-branding {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .tenant-icon {
            background: linear-gradient(135deg, var(--primary), var(--success));
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: #fff;
            box-shadow: 0 0 15px rgba(6, 182, 212, 0.3);
        }

        .tenant-info h1 {
            font-size: 1.35rem;
            font-weight: 700;
        }

        .tenant-info p {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .status-badge {
            background: rgba(6, 182, 212, 0.08);
            border: 1px solid rgba(6, 182, 212, 0.2);
            color: var(--primary);
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 2rem;
        }

        @media (max-width: 900px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Card panels */
        .panel {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            height: fit-content;
        }

        .panel-title {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 0.75rem;
        }

        /* Forms */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.4rem;
            font-size: 0.85rem;
            font-weight: 500;
            color: #d1d5db;
        }

        .form-input {
            width: 100%;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.75rem 0.9rem;
            color: #fff;
            font-family: inherit;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .btn {
            background: linear-gradient(135deg, var(--primary), #0891b2);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
        }

        .btn:hover {
            transform: translateY(-1px);
            filter: brightness(1.1);
            box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);
        }

        /* Tables */
        .users-table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
        }

        td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border);
            font-size: 0.9rem;
            color: #d1d5db;
        }

        tr:hover td {
            background-color: var(--table-hover);
        }

        tr:last-child td {
            border-bottom: none;
        }

        .empty-table {
            text-align: center;
            color: var(--text-muted);
            padding: 3rem 1rem !important;
        }

        /* Database details card */
        .meta-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
            font-size: 0.85rem;
        }

        .meta-item {
            display: flex;
            justify-content: space-between;
            background: rgba(0, 0, 0, 0.15);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.02);
        }

        .meta-label {
            color: var(--text-muted);
            font-weight: 500;
        }

        .meta-value {
            font-family: monospace;
            color: var(--primary);
            font-weight: 600;
        }

        .alert {
            padding: 0.8rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.25rem;
            font-size: 0.85rem;
            display: none;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #a7f3d0;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #fca5a5;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(6, 182, 212, 0.1);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .user-name-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="tenant-branding">
                <div class="tenant-icon">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div class="tenant-info">
                    <h1>{{ $client->name }}</h1>
                    <p>Subdomain: <span style="font-weight: 600; color: #fff;">{{ $client->subdomain }}</span></p>
                </div>
            </div>
            <div style="display: flex; gap: 15px; align-items: center;">
                <div class="status-badge">
                    <i class="fa-solid fa-database"></i> {{ $client->db_name }}
                </div>
                <form id="logoutForm" action="{{ route('tenant.logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn" style="background: transparent; border: 1px solid rgba(255,255,255,0.15); box-shadow: none; padding: 0.4rem 0.8rem; font-size: 0.85rem; width: auto; color: var(--text-muted);" onmouseover="this.style.color='#fff'; this.style.borderColor='rgba(255,255,255,0.35)'" onmouseout="this.style.color='var(--text-muted)'; this.style.borderColor='rgba(255,255,255,0.15)'">
                        <i class="fa-solid fa-right-from-bracket"></i> Sign Out ({{ $authUser->name }})
                    </button>
                </form>
                <a href="http://localhost{{ request()->getPort() ? ':'.request()->getPort() : '' }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='var(--text-muted)'">
                    <i class="fa-solid fa-arrow-left"></i> Exit to Portal
                </a>
            </div>
        </header>

        <div class="dashboard-grid">
            <!-- Left panel: Users table -->
            <div class="panel">
                <h2 class="panel-title">
                    <i class="fa-solid fa-users" style="color: var(--primary);"></i> Tenant Users
                </h2>
                
                <div class="users-table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email Address</th>
                                <th>Password (Plain)</th>
                                <th>Registered At</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="user-name-cell">
                                            <div class="user-avatar">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div style="font-weight: 600; color: #fff;">{{ $user->name }}</div>
                                                <div style="font-size: 0.75rem; color: var(--text-muted)">User ID: #{{ $user->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td><span style="font-family: monospace; background: rgba(255,255,255,0.06); padding: 0.2rem 0.5rem; border-radius: 4px; color: #a7f3d0;">{{ $user->password }}</span></td>
                                    <td>{{ $user->created_at ? $user->created_at->format('M d, Y h:i A') : 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr id="emptyTableRow">
                                    <td colspan="4" class="empty-table">
                                        <i class="fa-regular fa-folder-open" style="font-size: 2rem; margin-bottom: 0.5rem; display: block; color: var(--text-muted);"></i>
                                        No users registered in this tenant database yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right panel: Add user and database metadata -->
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <div class="panel">
                    <h2 class="panel-title">
                        <i class="fa-solid fa-user-plus" style="color: var(--primary);"></i> Add Tenant User
                    </h2>

                    <div class="alert alert-success" id="userSuccessAlert"></div>
                    <div class="alert alert-error" id="userErrorAlert"></div>

                    <form id="createUserForm">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="userName">Name</label>
                            <input class="form-input" type="text" id="userName" name="name" required placeholder="e.g. John Doe">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="userEmail">Email</label>
                            <input class="form-input" type="email" id="userEmail" name="email" required placeholder="e.g. john@acme.com">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="userPassword">Password</label>
                            <input class="form-input" type="password" id="userPassword" name="password" required placeholder="••••••••">
                        </div>

                        <button type="submit" class="btn">
                            <i class="fa-solid fa-save"></i> Save User to DB
                        </button>
                    </form>
                </div>

                <div class="panel">
                    <h2 class="panel-title">
                        <i class="fa-solid fa-circle-info" style="color: var(--primary);"></i> Database Isolation Info
                    </h2>
                    <ul class="meta-list">
                        <li class="meta-item">
                            <span class="meta-label">Client Name</span>
                            <span class="meta-value" style="color: #fff;">{{ $client->name }}</span>
                        </li>
                        <li class="meta-item">
                            <span class="meta-label">Subdomain</span>
                            <span class="meta-value">{{ $client->subdomain }}</span>
                        </li>
                        <li class="meta-item">
                            <span class="meta-label">DB Host</span>
                            <span class="meta-value">{{ $client->db_host }}</span>
                        </li>
                        <li class="meta-item">
                            <span class="meta-label">DB Name</span>
                            <span class="meta-value">{{ $client->db_name }}</span>
                        </li>
                        <li class="meta-item">
                            <span class="meta-label">DB User</span>
                            <span class="meta-value">{{ $client->db_username }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Form Submit via AJAX
            $('#createUserForm').on('submit', function(e) {
                e.preventDefault();
                $('#userSuccessAlert').hide();
                $('#userErrorAlert').hide();

                const formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('tenant.users.store') }}",
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            $('#userSuccessAlert').text(response.message).fadeIn();
                            $('#createUserForm')[0].reset();

                            // Remove empty row
                            $('#emptyTableRow').remove();

                            // Get initials
                            const name = response.user.name;
                            const initials = name.substring(0, 2).toUpperCase();
                            const formattedDate = new Date().toLocaleDateString('en-US', {
                                month: 'short',
                                day: '2-digit',
                                year: 'numeric'
                            }) + ' ' + new Date().toLocaleTimeString('en-US', {
                                hour: '2-digit',
                                minute: '2-digit'
                            });

                            // Append row dynamically
                            const newRow = `
                                <tr style="opacity: 0; background-color: rgba(6, 182, 212, 0.05);">
                                    <td>
                                        <div class="user-name-cell">
                                            <div class="user-avatar">
                                                ${initials}
                                            </div>
                                            <div>
                                                <div style="font-weight: 600; color: #fff;">${name}</div>
                                                <div style="font-size: 0.75rem; color: var(--text-muted)">User ID: #${response.user.id}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>${response.user.email}</td>
                                    <td><span style="font-family: monospace; background: rgba(255,255,255,0.06); padding: 0.2rem 0.5rem; border-radius: 4px; color: #a7f3d0;">${response.user.password}</span></td>
                                    <td>${formattedDate}</td>
                                </tr>
                            `;

                            const $newRowHtml = $(newRow);
                            $('#usersTableBody').prepend($newRowHtml);
                            
                            $newRowHtml.animate({ opacity: 1 }, 500, function() {
                                // Reset background color after animation
                                $(this).css('background-color', 'transparent');
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'An error occurred while creating the user.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            // Extract validation errors
                            errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        }
                        $('#userErrorAlert').html(errorMsg).fadeIn();
                    }
                });
            });
        });
    </script>
</body>
</html>
