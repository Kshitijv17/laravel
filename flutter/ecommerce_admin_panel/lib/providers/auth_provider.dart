import 'package:flutter/foundation.dart';
import '../models/user.dart';
import '../services/api_service.dart';

class AuthProvider with ChangeNotifier {
  User? _user;
  bool _isLoading = false;
  String? _error;

  User? get user => _user;
  bool get isLoading => _isLoading;
  String? get error => _error;
  bool get isAuthenticated => _user != null;

  void _setLoading(bool loading) {
    _isLoading = loading;
    notifyListeners();
  }

  void _setError(String? error) {
    _error = error;
    notifyListeners();
  }

  Future<bool> login(String email, String password) async {
    try {
      _setLoading(true);
      _setError(null);
      
      final authResponse = await ApiService.login(email, password);
      if (authResponse.user != null) {
        _user = authResponse.user;
      } else if (authResponse.admin != null) {
        // Convert Admin to User for compatibility
        final admin = authResponse.admin!;
        _user = User(
          id: admin.id,
          name: admin.name,
          email: admin.email,
          phone: admin.phone,
          profileImage: admin.avatar,
          emailVerifiedAt: admin.emailVerifiedAt,
          createdAt: admin.createdAt,
          updatedAt: admin.updatedAt,
        );
      }
      
      notifyListeners();
      return true;
    } catch (e) {
      _setError(e.toString());
      return false;
    } finally {
      _setLoading(false);
    }
  }

  Future<bool> register(String name, String email, String password, String phone) async {
    try {
      _setLoading(true);
      _setError(null);
      
      final authResponse = await ApiService.register(name, email, password, phone);
      _user = authResponse.user;
      
      notifyListeners();
      return true;
    } catch (e) {
      _setError(e.toString());
      return false;
    } finally {
      _setLoading(false);
    }
  }

  Future<void> logout() async {
    try {
      await ApiService.logout();
    } catch (e) {
      // Continue with logout even if API call fails
      debugPrint('Logout API error: $e');
    } finally {
      _user = null;
      notifyListeners();
    }
  }

  Future<void> checkAuthStatus() async {
    final token = await ApiService.getToken();
    if (token != null) {
      // Create a mock user for testing since we don't have a profile endpoint
      _user = User(
        id: 1,
        name: 'Admin User',
        email: 'admin@estore.com',
        phone: '+1234567890',
        profileImage: null,
        emailVerifiedAt: DateTime.now().toIso8601String(),
        createdAt: DateTime.now().subtract(const Duration(days: 30)).toIso8601String(),
        updatedAt: DateTime.now().toIso8601String(),
      );
      notifyListeners();
    }
  }

  Future<void> loadUser() async {
    try {
      _setLoading(true);
      _setError(null);
      
      // This would typically fetch current user data from API
      // For now, we'll just notify listeners to refresh UI
      notifyListeners();
    } catch (e) {
      _setError(e.toString());
    } finally {
      _setLoading(false);
    }
  }
}
