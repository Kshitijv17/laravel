import 'package:flutter/foundation.dart';
import '../models/order.dart';
import '../services/api_service.dart';

class DashboardProvider with ChangeNotifier {
  DashboardStats? _stats;
  bool _isLoading = false;
  String? _error;

  DashboardStats? get stats => _stats;
  bool get isLoading => _isLoading;
  String? get error => _error;

  void _setLoading(bool loading) {
    _isLoading = loading;
    notifyListeners();
  }

  void _setError(String? error) {
    _error = error;
    notifyListeners();
  }

  Future<void> loadDashboardStats() async {
    try {
      _setLoading(true);
      _setError(null);
      
      _stats = await ApiService.getDashboardStats();
      notifyListeners();
    } catch (e) {
      _setError(e.toString());
    } finally {
      _setLoading(false);
    }
  }

  void refresh() {
    loadDashboardStats();
  }
}
