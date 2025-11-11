module.exports = {
  apps: [{
    name: 'tempo-web',
    script: 'server.js',

    // Environment
    env: {
      NODE_ENV: 'production',
      PORT: 3000
    },

    // Instances
    instances: 1,
    exec_mode: 'fork',

    // Logging
    error_file: './logs/error.log',
    out_file: './logs/output.log',
    log_date_format: 'YYYY-MM-DD HH:mm:ss Z',
    merge_logs: true,

    // Auto restart
    autorestart: true,
    watch: false,
    max_memory_restart: '500M',

    // Restart delays
    min_uptime: '10s',
    max_restarts: 10,
    restart_delay: 4000,

    // Advanced
    kill_timeout: 5000,
    wait_ready: false,
    listen_timeout: 3000
  }]
};
