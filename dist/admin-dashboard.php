<?php
require_once __DIR__ . '/config.php';
requireAuth(); // Redirect to login if not authenticated
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard - Tempo</title>
    <link rel="stylesheet" href="/tailwind-styles.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f7fafc;
            min-height: 100vh;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.75rem;
            font-weight: bold;
        }

        .btn-logout {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.625rem 1.5rem;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }

        .btn-logout:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }

        .stat-card h3 {
            color: #718096;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        .stat-card .value {
            color: #2d3748;
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.25rem;
        }

        .stat-card .label {
            color: #a0aec0;
            font-size: 0.875rem;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .card h2 {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .db-info {
            display: grid;
            gap: 1rem;
        }

        .db-info-item {
            display: flex;
            padding: 0.75rem;
            background: #f7fafc;
            border-radius: 8px;
        }

        .db-info-item .label {
            font-weight: 600;
            color: #4a5568;
            min-width: 200px;
        }

        .db-info-item .value {
            color: #2d3748;
            font-family: 'Courier New', monospace;
        }

        .status-badge {
            display: inline-block;
            padding: 0.375rem 0.875rem;
            border-radius: 999px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .status-badge.connected {
            background: #c6f6d5;
            color: #22543d;
        }

        .status-badge.disconnected {
            background: #fed7d7;
            color: #742a2a;
        }

        .table-section {
            margin-top: 2rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-right: 0.5rem;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #2d3748;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-secondary:hover {
            background: #cbd5e0;
        }

        .loading {
            text-align: center;
            padding: 2rem;
            color: #718096;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            background: #f7fafc;
            font-weight: 600;
            color: #4a5568;
            text-transform: uppercase;
            font-size: 0.75rem;
        }

        tr:hover {
            background: #f7fafc;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s;
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .modal-header h3 {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2d3748;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #718096;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .close-btn:hover {
            background: #e2e8f0;
            color: #2d3748;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2d3748;
            font-size: 0.875rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: border-color 0.3s;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
        }

        .modal-footer {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-success {
            background: #48bb78;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            flex: 1;
            transition: background 0.2s;
        }

        .btn-success:hover {
            background: #38a169;
        }

        .btn-cancel {
            background: #e2e8f0;
            color: #2d3748;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            flex: 1;
            transition: background 0.2s;
        }

        .btn-cancel:hover {
            background: #cbd5e0;
        }

        .clients-section {
            margin-top: 2rem;
        }

        .expiring-soon {
            background-color: #fed7d7 !important;
        }

        .expiring-soon:hover {
            background-color: #fc8181 !important;
        }

        /* Mobile responsive styles */
        @media (max-width: 768px) {
            .header {
                padding: 1rem;
            }

            .header-content {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .header h1 {
                font-size: 1.25rem;
            }

            .container {
                padding: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .card {
                padding: 1rem;
            }

            .card h2 {
                font-size: 1.25rem;
            }

            .table-container {
                font-size: 0.875rem;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table-container table {
                min-width: 800px;
            }

            th, td {
                padding: 0.5rem 0.25rem;
                font-size: 0.75rem;
                white-space: nowrap;
            }

            .btn-primary, .btn-secondary {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
                width: 100%;
                margin-right: 0 !important;
                margin-bottom: 0.5rem;
            }

            .card > div[style*="margin-bottom"] {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }

            .modal-content {
                width: 95%;
                padding: 1rem;
                max-height: 85vh;
            }

            .modal-header h3 {
                font-size: 1.25rem;
            }

            .form-input {
                padding: 0.625rem;
                font-size: 0.875rem;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>🔐 Tempo Super Admin</h1>
            <button class="btn-logout" onclick="logout()">Logout</button>
        </div>
    </div>

    <div class="container">
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Database Status</h3>
                <div class="value" id="dbStatus">
                    <span class="status-badge connected">Connected</span>
                </div>
                <div class="label">Active Connection</div>
            </div>

            <div class="stat-card">
                <h3>Total Subscribers</h3>
                <div class="value" id="totalSubscribers">-</div>
                <div class="label">Active Subscribers</div>
            </div>

            <div class="stat-card">
                <h3>Database Name</h3>
                <div class="value" style="font-size: 1.25rem;">incjzljm_tempo_app_main</div>
                <div class="label">Production Database</div>
            </div>

            <div class="stat-card">
                <h3>Server Time</h3>
                <div class="value" id="serverTime" style="font-size: 1.25rem;">--:--:--</div>
                <div class="label" id="serverDate">Loading...</div>
            </div>
        </div>

        <!-- Clients/Subscribers Section -->
        <div class="card">
            <h2>Clients / Subscribers</h2>
            <div style="margin-bottom: 1rem;">
                <button class="btn-primary" onclick="openAddSubscriberModal()">➕ Add Subscriber</button>
                <button class="btn-secondary" onclick="loadClients()">🔄 Refresh Clients</button>
            </div>
            <div class="table-container" id="clientsContainer">
                <div class="loading">Click "Refresh Clients" to load subscribers...</div>
            </div>
        </div>

        <!-- Database Information -->
        <div class="card">
            <h2>Database Information</h2>
            <div class="db-info" id="dbInfo">
                <div class="loading">Loading database information...</div>
            </div>
        </div>
    </div>

    <!-- Add Subscriber Modal -->
    <div id="subscriberModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New Subscriber</h3>
                <button class="close-btn" onclick="closeSubscriberModal()">&times;</button>
            </div>
            <form id="subscriberForm">
                <div class="form-group">
                    <label for="clientName">Name *</label>
                    <input type="text" id="clientName" name="name" class="form-input" required maxlength="255">
                </div>

                <div class="form-group">
                    <label for="dateCreated">Date Created *</label>
                    <input type="datetime-local" id="dateCreated" name="date_created" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="expiry">Expiry Date *</label>
                    <input type="date" id="expiry" name="expiry" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="subscriptionType">Subscription Type *</label>
                    <input type="text" id="subscriptionType" name="subscription_type" class="form-input" required maxlength="255" placeholder="e.g., Premium, Basic, Pro">
                </div>

                <div class="form-group">
                    <label for="maxClients">Max Clients *</label>
                    <input type="number" id="maxClients" name="max_clients" class="form-input" required min="0">
                </div>

                <div class="form-group">
                    <label for="maxUsers">Max Users *</label>
                    <input type="number" id="maxUsers" name="max_users" class="form-input" required min="0">
                </div>

                <div class="form-group">
                    <label for="link">Link</label>
                    <input type="text" id="link" name="link" class="form-input" maxlength="255" placeholder="https://example.com">
                </div>

                <div class="form-group">
                    <label for="contactInfo">Contact Info</label>
                    <input type="text" id="contactInfo" name="contact_info" class="form-input" maxlength="255" placeholder="email@example.com or phone">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeSubscriberModal()">Cancel</button>
                    <button type="submit" class="btn-success">Save Subscriber</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Subscriber Modal -->
    <div id="editSubscriberModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Subscriber</h3>
                <button class="close-btn" onclick="closeEditSubscriberModal()">&times;</button>
            </div>
            <form id="editSubscriberForm">
                <input type="hidden" id="editClientId" name="id">

                <div class="form-group">
                    <label for="editClientName">Name *</label>
                    <input type="text" id="editClientName" name="name" class="form-input" required maxlength="255">
                </div>

                <div class="form-group">
                    <label for="editDateCreated">Date Created *</label>
                    <input type="datetime-local" id="editDateCreated" name="date_created" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="editExpiry">Expiry Date *</label>
                    <input type="date" id="editExpiry" name="expiry" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="editSubscriptionType">Subscription Type *</label>
                    <input type="text" id="editSubscriptionType" name="subscription_type" class="form-input" required maxlength="255" placeholder="e.g., Premium, Basic, Pro">
                </div>

                <div class="form-group">
                    <label for="editMaxClients">Max Clients *</label>
                    <input type="number" id="editMaxClients" name="max_clients" class="form-input" required min="0">
                </div>

                <div class="form-group">
                    <label for="editMaxUsers">Max Users *</label>
                    <input type="number" id="editMaxUsers" name="max_users" class="form-input" required min="0">
                </div>

                <div class="form-group">
                    <label for="editLink">Link</label>
                    <input type="text" id="editLink" name="link" class="form-input" maxlength="255" placeholder="https://example.com">
                </div>

                <div class="form-group">
                    <label for="editContactInfo">Contact Info</label>
                    <input type="text" id="editContactInfo" name="contact_info" class="form-input" maxlength="255" placeholder="email@example.com or phone">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditSubscriberModal()">Cancel</button>
                    <button type="submit" class="btn-success">Update Subscriber</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Update server time
        function updateTime() {
            const now = new Date();
            document.getElementById('serverTime').textContent = now.toLocaleTimeString('ro-RO');
            document.getElementById('serverDate').textContent = now.toLocaleDateString('ro-RO', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
        setInterval(updateTime, 1000);
        updateTime();

        // Load database info on page load
        async function loadDatabaseInfo() {
            try {
                const response = await fetch('/admin-api.php?action=database-info');
                const data = await response.json();

                if (data.success) {
                    const info = data.data;
                    document.getElementById('dbInfo').innerHTML = `
                        <div class="db-info-item">
                            <span class="label">Database Name:</span>
                            <span class="value">${info.database}</span>
                        </div>
                        <div class="db-info-item">
                            <span class="label">Host:</span>
                            <span class="value">${info.host}</span>
                        </div>
                        <div class="db-info-item">
                            <span class="label">Port:</span>
                            <span class="value">${info.port}</span>
                        </div>
                        <div class="db-info-item">
                            <span class="label">User:</span>
                            <span class="value">${info.user}</span>
                        </div>
                        <div class="db-info-item">
                            <span class="label">Connection Status:</span>
                            <span class="value"><span class="status-badge ${info.connected ? 'connected' : 'disconnected'}">${info.connected ? 'Connected' : 'Disconnected'}</span></span>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading database info:', error);
            }
        }

        // Load tables
        async function loadTables() {
            document.getElementById('tablesContainer').innerHTML = '<div class="loading">Loading tables...</div>';

            try {
                const response = await fetch('/admin-api.php?action=tables');
                const data = await response.json();

                if (data.success && data.data.length > 0) {
                    document.getElementById('totalTables').textContent = data.data.length;

                    let html = '<table><thead><tr><th>Table Name</th><th>Actions</th></tr></thead><tbody>';
                    data.data.forEach(table => {
                        html += `
                            <tr>
                                <td><strong>${table.name}</strong></td>
                                <td>
                                    <button class="btn-secondary" style="padding: 0.5rem 1rem; margin: 0;" onclick="viewTable('${table.name}')">View Data</button>
                                </td>
                            </tr>
                        `;
                    });
                    html += '</tbody></table>';
                    document.getElementById('tablesContainer').innerHTML = html;
                } else {
                    document.getElementById('tablesContainer').innerHTML = '<div class="loading">No tables found in database</div>';
                }
            } catch (error) {
                console.error('Error loading tables:', error);
                document.getElementById('tablesContainer').innerHTML = '<div class="loading" style="color: #c33;">Error loading tables</div>';
            }
        }

        // Test connection
        async function testConnection() {
            try {
                const response = await fetch('/admin-api.php?action=test-connection');
                const data = await response.json();
                alert(data.success ? '✅ Connection successful!' : '❌ Connection failed!');
            } catch (error) {
                alert('❌ Connection test failed!');
            }
        }

        // View table data
        function viewTable(tableName) {
            alert(`Viewing table: ${tableName}\n\nThis feature will be implemented to show table data.`);
        }

        // Show query editor
        function showQueryEditor() {
            alert('SQL Query Editor\n\nThis feature will be implemented to allow running custom SQL queries.');
        }

        // View system info
        function viewSystemInfo() {
            alert('System Information\n\nThis feature will be implemented to show server and system details.');
        }

        // Logout
        async function logout() {
            try {
                const response = await fetch('/admin-logout.php', { method: 'POST' });
                if (response.ok) {
                    window.location.href = '/admin-login.php';
                }
            } catch (error) {
                console.error('Logout error:', error);
            }
        }

        // Modal functions
        function openAddSubscriberModal() {
            const modal = document.getElementById('subscriberModal');
            const now = new Date();

            // Set default date created to now
            const dateCreatedInput = document.getElementById('dateCreated');
            dateCreatedInput.value = now.toISOString().slice(0, 16);

            // Set default expiry to 1 year from now
            const expiryDate = new Date();
            expiryDate.setFullYear(expiryDate.getFullYear() + 1);
            document.getElementById('expiry').value = expiryDate.toISOString().slice(0, 10);

            modal.classList.add('show');
        }

        function closeSubscriberModal() {
            const modal = document.getElementById('subscriberModal');
            modal.classList.remove('show');
            document.getElementById('subscriberForm').reset();
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('subscriberModal');
            const editModal = document.getElementById('editSubscriberModal');
            if (event.target === modal) {
                closeSubscriberModal();
            }
            if (event.target === editModal) {
                closeEditSubscriberModal();
            }
        }

        // Edit modal functions
        function openEditSubscriberModal(client) {
            const modal = document.getElementById('editSubscriberModal');

            // Populate form fields with existing data
            document.getElementById('editClientId').value = client.id;
            document.getElementById('editClientName').value = client.name || '';

            // Convert date_created to datetime-local format
            if (client.date_created) {
                const dateCreated = new Date(client.date_created);
                document.getElementById('editDateCreated').value = dateCreated.toISOString().slice(0, 16);
            }

            // Set expiry date
            document.getElementById('editExpiry').value = client.expiry || '';

            document.getElementById('editSubscriptionType').value = client.subscription_type || '';
            document.getElementById('editMaxClients').value = client.max_clients || 0;
            document.getElementById('editMaxUsers').value = client.max_users || 0;
            document.getElementById('editLink').value = client.link || '';
            document.getElementById('editContactInfo').value = client.contact_info || '';

            modal.classList.add('show');
        }

        function closeEditSubscriberModal() {
            const modal = document.getElementById('editSubscriberModal');
            modal.classList.remove('show');
            document.getElementById('editSubscriberForm').reset();
        }

        // Handle subscriber form submission
        document.getElementById('subscriberForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(e.target);
            const data = {
                name: formData.get('name'),
                date_created: formData.get('date_created'),
                expiry: formData.get('expiry'),
                subscription_type: formData.get('subscription_type'),
                max_clients: parseInt(formData.get('max_clients')),
                max_users: parseInt(formData.get('max_users')),
                link: formData.get('link') || '',
                contact_info: formData.get('contact_info') || ''
            };

            try {
                const response = await fetch('/admin-api.php?action=add-subscriber', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    alert('✅ Subscriber added successfully!');
                    closeSubscriberModal();
                    loadClients(); // Reload the clients table
                } else {
                    alert('❌ Error: ' + (result.message || 'Failed to add subscriber'));
                }
            } catch (error) {
                console.error('Error adding subscriber:', error);
                alert('❌ Error adding subscriber. Please try again.');
            }
        });

        // Handle edit subscriber form submission
        document.getElementById('editSubscriberForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(e.target);
            const data = {
                id: parseInt(formData.get('id')),
                name: formData.get('name'),
                date_created: formData.get('date_created'),
                expiry: formData.get('expiry'),
                subscription_type: formData.get('subscription_type'),
                max_clients: parseInt(formData.get('max_clients')),
                max_users: parseInt(formData.get('max_users')),
                link: formData.get('link') || '',
                contact_info: formData.get('contact_info') || ''
            };

            console.log('Updating subscriber with data:', data);

            try {
                const response = await fetch('/admin-api.php?action=update-subscriber', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                console.log('Update response:', result);

                if (result.success) {
                    alert('✅ Subscriber updated successfully!');
                    closeEditSubscriberModal();
                    loadClients(); // Reload the clients table
                } else {
                    console.error('Update failed:', result);
                    alert('❌ Error: ' + (result.message || 'Failed to update subscriber'));
                }
            } catch (error) {
                console.error('Error updating subscriber:', error);
                alert('❌ Error updating subscriber. Please try again.');
            }
        });

        // Load clients data
        async function loadClients() {
            document.getElementById('clientsContainer').innerHTML = '<div class="loading">Loading clients...</div>';

            try {
                const response = await fetch('/admin-api.php?action=get-clients');
                const data = await response.json();

                if (data.success && data.data.length > 0) {
                    // Update total subscribers count
                    document.getElementById('totalSubscribers').textContent = data.data.length;

                    let html = '<table><thead><tr>';
                    html += '<th>ID</th>';
                    html += '<th>Name</th>';
                    html += '<th>Date Created</th>';
                    html += '<th>Expiry</th>';
                    html += '<th>Subscription Type</th>';
                    html += '<th>Max Clients</th>';
                    html += '<th>Max Users</th>';
                    html += '<th>Link</th>';
                    html += '<th>Contact Info</th>';
                    html += '<th>Actions</th>';
                    html += '</tr></thead><tbody>';

                    const today = new Date();
                    const tenDaysFromNow = new Date();
                    tenDaysFromNow.setDate(today.getDate() + 10);

                    data.data.forEach(client => {
                        // Check if expiry is within 10 days
                        let rowClass = '';
                        if (client.expiry) {
                            const expiryDate = new Date(client.expiry);
                            if (expiryDate <= tenDaysFromNow) {
                                rowClass = ' class="expiring-soon"';
                            }
                        }

                        html += `<tr${rowClass}>`;
                        html += `<td>${client.id || '-'}</td>`;
                        html += `<td><strong>${client.name || '-'}</strong></td>`;
                        html += `<td>${client.date_created || '-'}</td>`;
                        html += `<td>${client.expiry || '-'}</td>`;
                        html += `<td>${client.subscription_type || '-'}</td>`;
                        html += `<td>${client.max_clients || '0'}</td>`;
                        html += `<td>${client.max_users || '0'}</td>`;
                        html += `<td>${client.link ? `<a href="${client.link}" target="_blank" style="color: #667eea;">${client.link}</a>` : '-'}</td>`;
                        html += `<td>${client.contact_info || '-'}</td>`;
                        html += `<td><button class="btn-secondary" style="padding: 0.5rem 1rem; margin: 0;" onclick='openEditSubscriberModal(${JSON.stringify(client)})'>✏️ Edit</button></td>`;
                        html += '</tr>';
                    });

                    html += '</tbody></table>';
                    document.getElementById('clientsContainer').innerHTML = html;
                } else {
                    document.getElementById('totalSubscribers').textContent = '0';
                    document.getElementById('clientsContainer').innerHTML = '<div class="loading">No clients found. Click "Add Subscriber" to add one.</div>';
                }
            } catch (error) {
                console.error('Error loading clients:', error);
                document.getElementById('clientsContainer').innerHTML = '<div class="loading" style="color: #c33;">Error loading clients</div>';
            }
        }

        // Load initial data
        loadDatabaseInfo();
        loadClients(); // Auto-load clients on page load
    </script>
</body>
</html>
