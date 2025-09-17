import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class UserApiService {
  static const String baseUrl = 'http://127.0.0.1:8080/api/v1';
  static String? _token;

  // Get stored token
  static Future<String?> getToken() async {
    if (_token != null) return _token;
    final prefs = await SharedPreferences.getInstance();
    _token = prefs.getString('auth_token');
    return _token;
  }

  // Save token
  static Future<void> saveToken(String token) async {
    _token = token;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('auth_token', token);
  }

  // Clear token
  static Future<void> clearToken() async {
    _token = null;
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
  }

  // Get headers with authentication
  static Future<Map<String, String>> _getHeaders() async {
    final token = await getToken();
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    };
  }

  // Handle API response
  static Map<String, dynamic> _handleResponse(http.Response response) {
    if (response.statusCode >= 200 && response.statusCode < 300) {
      return json.decode(response.body);
    } else {
      throw Exception('API Error: ${response.statusCode} - ${response.body}');
    }
  }

  // Authentication APIs
  static Future<Map<String, dynamic>> register({
    required String name,
    required String email,
    required String password,
    required String phone,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/auth/register'),
      headers: await _getHeaders(),
      body: json.encode({
        'name': name,
        'email': email,
        'password': password,
        'password_confirmation': password,
        'phone': phone,
      }),
    );

    final data = _handleResponse(response);
    if (data['data']['token'] != null) {
      await saveToken(data['data']['token']);
    }
    return data;
  }

  static Future<Map<String, dynamic>> login({
    required String email,
    required String password,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/auth/login'),
      headers: await _getHeaders(),
      body: json.encode({
        'email': email,
        'password': password,
      }),
    );

    final data = _handleResponse(response);
    if (data['data']['token'] != null) {
      await saveToken(data['data']['token']);
    }
    return data;
  }

  static Future<void> logout() async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/logout'),
        headers: await _getHeaders(),
      );
      _handleResponse(response);
    } finally {
      await clearToken();
    }
  }

  // Product APIs
  static Future<Map<String, dynamic>> getProducts({
    int page = 1,
    int perPage = 10,
    String? search,
    int? categoryId,
    double? minPrice,
    double? maxPrice,
    bool? featured,
  }) async {
    final queryParams = <String, String>{
      'page': page.toString(),
      'per_page': perPage.toString(),
    };

    if (search != null && search.isNotEmpty) {
      queryParams['search'] = search;
    }
    if (categoryId != null) {
      queryParams['category_id'] = categoryId.toString();
    }
    if (minPrice != null) {
      queryParams['min_price'] = minPrice.toString();
    }
    if (maxPrice != null) {
      queryParams['max_price'] = maxPrice.toString();
    }
    if (featured != null) {
      queryParams['featured'] = featured ? '1' : '0';
    }

    final uri = Uri.parse('$baseUrl/products').replace(queryParameters: queryParams);
    final response = await http.get(uri, headers: await _getHeaders());
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> getProduct({required String productId}) async {
    final response = await http.get(
      Uri.parse('$baseUrl/products/$productId'),
      headers: await _getHeaders(),
    );
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> searchProducts({required String query}) async {
    final response = await http.get(
      Uri.parse('$baseUrl/products/search?query=$query'),
      headers: await _getHeaders(),
    );
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> getFeaturedProducts() async {
    final response = await http.get(
      Uri.parse('$baseUrl/products/featured'),
      headers: await _getHeaders(),
    );
    return _handleResponse(response);
  }

  // Category APIs
  static Future<Map<String, dynamic>> getCategories() async {
    final response = await http.get(
      Uri.parse('$baseUrl/categories'),
      headers: await _getHeaders(),
    );
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> getCategory(int categoryId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/categories/$categoryId'),
      headers: await _getHeaders(),
    );
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> getProductsByCategory({required String categoryId}) async {
    final response = await http.get(
      Uri.parse('$baseUrl/categories/$categoryId/products'),
      headers: await _getHeaders(),
    );
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> getCategoryProducts(int categoryId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/categories/$categoryId/products'),
      headers: await _getHeaders(),
    );
    return _handleResponse(response);
  }

  // Cart APIs
  static Future<Map<String, dynamic>> getCart() async {
    final response = await http.get(
      Uri.parse('$baseUrl/cart'),
      headers: await _getHeaders(),
    );
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> addToCart({
    required String productId,
    required int quantity,
    String? color,
    String? size,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/cart/add'),
      headers: await _getHeaders(),
      body: json.encode({
        'product_id': productId,
        'quantity': quantity,
        if (color != null) 'color': color,
        if (size != null) 'size': size,
      }),
    );
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> updateCartItem({
    required int cartItemId,
    required int quantity,
  }) async {
    final response = await http.put(
      Uri.parse('$baseUrl/cart/$cartItemId'),
      headers: await _getHeaders(),
      body: json.encode({
        'quantity': quantity,
      }),
    );
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> removeFromCart(int cartItemId) async {
    final response = await http.delete(
      Uri.parse('$baseUrl/cart/$cartItemId'),
      headers: await _getHeaders(),
    );
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> clearCart() async {
    final response = await http.delete(
      Uri.parse('$baseUrl/cart/clear'),
      headers: await _getHeaders(),
    );
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> getCartSummary() async {
    final response = await http.get(
      Uri.parse('$baseUrl/cart/summary'),
      headers: await _getHeaders(),
    );
    return _handleResponse(response);
  }

  // Wishlist APIs
  static Future<Map<String, dynamic>> getWishlists() async {
    final response = await http.get(
      Uri.parse('$baseUrl/wishlists'),
      headers: await _getHeaders(),
    );
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> createWishlist({
    required String name,
    String? description,
    bool isPublic = false,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/wishlists'),
      headers: await _getHeaders(),
      body: json.encode({
        'name': name,
        'description': description,
        'is_public': isPublic,
      }),
    );
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> addToWishlist({
    required String productId,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/wishlist/add'),
      headers: await _getHeaders(),
      body: json.encode({
        'product_id': productId,
      }),
    );
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> removeFromWishlist({
    required String productId,
  }) async {
    final response = await http.delete(
      Uri.parse('$baseUrl/wishlist/remove'),
      headers: await _getHeaders(),
      body: json.encode({
        'product_id': productId,
      }),
    );
    return _handleResponse(response);
  }

  // Order APIs
  static Future<Map<String, dynamic>> getOrders() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/orders'),
        headers: await _getHeaders(),
      );
      
      // Handle different response scenarios
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        
        // If successful but no orders, return empty structure
        if (data['data'] == null || (data['data'] is List && data['data'].isEmpty)) {
          return {
            'success': true,
            'data': [],
            'message': 'No orders found'
          };
        }
        
        return data;
      } else if (response.statusCode == 404) {
        // Orders endpoint not found or no orders
        return {
          'success': true,
          'data': [],
          'message': 'No orders found'
        };
      } else {
        throw Exception('Failed to load orders: ${response.statusCode}');
      }
    } catch (e) {
      print('Order API Error: $e');
      // Return empty structure instead of throwing
      return {
        'success': false,
        'data': [],
        'error': e.toString(),
        'message': 'Failed to load orders'
      };
    }
  }

  static Future<Map<String, dynamic>> createOrder({
    required Map<String, dynamic> orderData,
    String? notes,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/orders'),
      headers: await _getHeaders(),
      body: json.encode(orderData),
    );
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> cancelOrder(int orderId) async {
    final response = await http.post(
      Uri.parse('$baseUrl/orders/$orderId/cancel'),
      headers: await _getHeaders(),
    );
    return _handleResponse(response);
  }

  // Address APIs
  static Future<Map<String, dynamic>> getAddresses() async {
    final response = await http.get(
      Uri.parse('$baseUrl/addresses'),
      headers: await _getHeaders(),
    );
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> createAddress({
    required String type,
    required String firstName,
    required String lastName,
    String? company,
    required String addressLine1,
    String? addressLine2,
    required String city,
    required String state,
    required String postalCode,
    required String country,
    required String phone,
    bool isDefault = false,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/addresses'),
      headers: await _getHeaders(),
      body: json.encode({
        'type': type,
        'first_name': firstName,
        'last_name': lastName,
        'company': company,
        'address_line_1': addressLine1,
        'address_line_2': addressLine2,
        'city': city,
        'state': state,
        'postal_code': postalCode,
        'country': country,
        'phone': phone,
        'is_default': isDefault,
      }),
    );
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> setDefaultAddress(int addressId) async {
    final response = await http.post(
      Uri.parse('$baseUrl/addresses/$addressId/set-default'),
      headers: await _getHeaders(),
    );
    return _handleResponse(response);
  }

  // Profile APIs
  static Future<Map<String, dynamic>> getProfile() async {
    final response = await http.get(
      Uri.parse('$baseUrl/profile'),
      headers: await _getHeaders(),
    );
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> updateProfile({
    required String name,
    String? phone,
    String? dateOfBirth,
    String? gender,
  }) async {
    final response = await http.put(
      Uri.parse('$baseUrl/profile'),
      headers: await _getHeaders(),
      body: json.encode({
        'name': name,
        'phone': phone,
        'date_of_birth': dateOfBirth,
        'gender': gender,
      }),
    );
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> changePassword({
    required String currentPassword,
    required String newPassword,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/change-password'),
      headers: await _getHeaders(),
      body: json.encode({
        'current_password': currentPassword,
        'new_password': newPassword,
      }),
    );
    return _handleResponse(response);
  }

  // Review APIs
  static Future<Map<String, dynamic>> createReview({
    required int productId,
    required int rating,
    required String title,
    required String comment,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/reviews'),
      headers: await _getHeaders(),
      body: json.encode({
        'product_id': productId,
        'rating': rating,
        'title': title,
        'comment': comment,
      }),
    );
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> getProductReviews(int productId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/products/$productId/reviews'),
      headers: await _getHeaders(),
    );
    return _handleResponse(response);
  }

  // Payment APIs
  static Future<Map<String, dynamic>> processPayment({
    required int orderId,
    required String paymentMethod,
    required double amount,
    String currency = 'USD',
    Map<String, dynamic>? paymentDetails,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/payments'),
      headers: await _getHeaders(),
      body: json.encode({
        'order_id': orderId,
        'payment_method': paymentMethod,
        'amount': amount,
        'currency': currency,
        'payment_details': paymentDetails,
      }),
    );
    return _handleResponse(response);
  }

  // Support APIs
  static Future<Map<String, dynamic>> createSupportTicket({
    required String subject,
    required String description,
    String priority = 'medium',
    String category = 'general',
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/support-tickets'),
      headers: await _getHeaders(),
      body: json.encode({
        'subject': subject,
        'description': description,
        'priority': priority,
        'category': category,
      }),
    );
    return _handleResponse(response);
  }

  // Newsletter APIs
  static Future<Map<String, dynamic>> subscribeNewsletter({
    required String email,
    String? name,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/newsletter/subscribe'),
      headers: await _getHeaders(),
      body: json.encode({
        'email': email,
        'name': name,
      }),
    );
    return _handleResponse(response);
  }

  // Public APIs (no authentication required)
  static Future<Map<String, dynamic>> getBanners({String? position}) async {
    final queryParams = <String, String>{};
    if (position != null) {
      queryParams['position'] = position;
    }

    final uri = Uri.parse('$baseUrl/banners/position').replace(queryParameters: queryParams);
    final response = await http.get(uri);
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> getFAQs() async {
    final response = await http.get(Uri.parse('$baseUrl/faqs?active=1'));
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> trackOrder(String trackingNumber) async {
    final response = await http.get(
      Uri.parse('$baseUrl/tracking/$trackingNumber'),
    );
    return _handleResponse(response);
  }
}
