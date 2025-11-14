const bcrypt = require('bcryptjs');
require('dotenv').config();

/**
 * Middleware to check if user is authenticated as admin
 */
const isAuthenticated = (req, res, next) => {
  if (req.session && req.session.isAdmin) {
    return next();
  }
  res.redirect('/admin/login');
};

/**
 * Verify admin credentials
 */
const verifyAdminCredentials = async (username, password) => {
  const adminUsername = process.env.ADMIN_USERNAME || 'admin';
  const adminPassword = process.env.ADMIN_PASSWORD || 'ChangeThisPassword123!';

  // Check username
  if (username !== adminUsername) {
    return false;
  }

  // For simplicity, we're doing direct password comparison
  // In production, you should hash the password in .env and compare hashes
  if (password === adminPassword) {
    return true;
  }

  // Also support bcrypt hashed passwords
  try {
    const isMatch = await bcrypt.compare(password, adminPassword);
    return isMatch;
  } catch (error) {
    // If adminPassword is not hashed, bcrypt.compare will fail
    // We already did direct comparison above, so return false here
    return false;
  }
};

/**
 * Hash a password (utility function)
 */
const hashPassword = async (password) => {
  const salt = await bcrypt.genSalt(10);
  return await bcrypt.hash(password, salt);
};

module.exports = {
  isAuthenticated,
  verifyAdminCredentials,
  hashPassword
};
