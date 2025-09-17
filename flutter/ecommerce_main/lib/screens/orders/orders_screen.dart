import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';
import '../../services/user_api_service.dart';

class OrdersScreen extends StatefulWidget {
  const OrdersScreen({super.key});

  @override
  State<OrdersScreen> createState() => _OrdersScreenState();
}

class _OrdersScreenState extends State<OrdersScreen> {
  List<Map<String, dynamic>> _orders = [];
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadOrders();
  }

  Future<void> _loadOrders() async {
    try {
      setState(() {
        _isLoading = true;
        _error = null;
      });

      final response = await UserApiService.getOrders();
      
      // Handle different response structures
      List<Map<String, dynamic>> orders = [];
      
      // Check if API returned data successfully
      if (response['success'] == true || response.containsKey('data')) {
        final ordersData = response['data'];
        if (ordersData is List && ordersData.isNotEmpty) {
          // Process each order to ensure proper field mapping
          orders = ordersData.map<Map<String, dynamic>>((order) {
            return {
              'id': order['id']?.toString() ?? '',
              'status': _mapOrderStatus(order['status']?.toString() ?? order['order_status']?.toString() ?? 'Processing'),
              'date': _formatOrderDate(order['created_at']?.toString() ?? order['order_date']?.toString() ?? ''),
              'product_name': _extractProductName(order),
              'size': order['size']?.toString() ?? order['product_size']?.toString() ?? 'N/A',
              'quantity': order['quantity']?.toString() ?? order['qty']?.toString() ?? '1',
              'image': _extractProductImage(order),
              'total_amount': _formatPrice(order['total']?.toString() ?? order['total_amount']?.toString() ?? order['amount']?.toString() ?? '0'),
            };
          }).toList();
        }
      }

      // If no live data available, use mock data for demo
      if (orders.isEmpty) {
        orders = _getMockOrders();
        if (mounted && response['success'] == false) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: const Text('Using demo data - API connection issue'),
              backgroundColor: Colors.orange,
              action: SnackBarAction(
                label: 'Retry',
                textColor: Colors.white,
                onPressed: _loadOrders,
              ),
            ),
          );
        }
      }

      setState(() {
        _orders = orders;
        _isLoading = false;
      });
    } catch (e) {
      print('Error loading orders: $e');
      
      // Use mock data as fallback
      setState(() {
        _orders = _getMockOrders();
        _isLoading = false;
        _error = e.toString();
      });
      
      // Show error message to user
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: const Text('Using demo data - Backend connection failed'),
            backgroundColor: Colors.orange,
            action: SnackBarAction(
              label: 'Retry',
              textColor: Colors.white,
              onPressed: _loadOrders,
            ),
          ),
        );
      }
    }
  }

  String _mapOrderStatus(String status) {
    switch (status.toLowerCase()) {
      case 'pending':
      case 'processing':
        return 'Order Processing';
      case 'shipped':
      case 'dispatched':
        return 'Order Shipped';
      case 'delivered':
      case 'completed':
        return 'Order Delivered';
      case 'cancelled':
      case 'canceled':
        return 'Order Cancelled';
      case 'returned':
        return 'Order Returned';
      default:
        return 'Order $status';
    }
  }

  String _formatOrderDate(String dateStr) {
    if (dateStr.isEmpty) return 'Unknown Date';
    
    try {
      final date = DateTime.parse(dateStr);
      return '${date.day}/${date.month}/${date.year}';
    } catch (e) {
      return dateStr;
    }
  }

  String _extractProductName(Map<String, dynamic> order) {
    // Try different possible field names for product name
    return order['product_name']?.toString() ??
           order['name']?.toString() ??
           order['product']?['name']?.toString() ??
           order['items']?[0]?['product_name']?.toString() ??
           order['order_items']?[0]?['product_name']?.toString() ??
           'Product';
  }

  String _extractProductImage(Map<String, dynamic> order) {
    // Try different possible field names for product image
    return order['image']?.toString() ??
           order['product_image']?.toString() ??
           order['product']?['image']?.toString() ??
           order['items']?[0]?['image']?.toString() ??
           order['order_items']?[0]?['product_image']?.toString() ??
           '';
  }

  String _formatPrice(String priceStr) {
    if (priceStr.isEmpty || priceStr == '0') return '₹0';
    
    try {
      final price = double.parse(priceStr);
      return '₹${price.toStringAsFixed(0)}';
    } catch (e) {
      return priceStr.startsWith('₹') ? priceStr : '₹$priceStr';
    }
  }

  List<Map<String, dynamic>> _getMockOrders() {
    return [
      {
        'id': '1',
        'status': 'Order Delivered',
        'date': '2024-01-15',
        'product_name': 'Cotton Kurti for Women',
        'size': 'M',
        'quantity': '1',
        'image': 'https://via.placeholder.com/150/FF6B6B/FFFFFF?text=Kurti',
        'total_amount': '₹599',
      },
      {
        'id': '2',
        'status': 'Order Shipped',
        'date': '2024-01-10',
        'product_name': 'Men\'s Casual Shirt',
        'size': 'L',
        'quantity': '2',
        'image': 'https://via.placeholder.com/150/4ECDC4/FFFFFF?text=Shirt',
        'total_amount': '₹1299',
      },
      {
        'id': '3',
        'status': 'Order Processing',
        'date': '2024-01-08',
        'product_name': 'Women\'s Ethnic Dress',
        'size': 'S',
        'quantity': '1',
        'image': 'https://via.placeholder.com/150/45B7D1/FFFFFF?text=Dress',
        'total_amount': '₹899',
      },
      {
        'id': '4',
        'status': 'Order Cancelled',
        'date': '2024-01-05',
        'product_name': 'Kids T-Shirt',
        'size': 'XS',
        'quantity': '3',
        'image': 'https://via.placeholder.com/150/F7DC6F/FFFFFF?text=Kids',
        'total_amount': '₹750',
      },
    ];
  }


  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text(
          'MY ORDERS',
          style: TextStyle(
            fontWeight: FontWeight.w600,
            color: Colors.black87,
            fontSize: 16,
          ),
        ),
        backgroundColor: Colors.white,
        elevation: 0,
        centerTitle: false,
        iconTheme: const IconThemeData(color: Colors.black87),
        actions: [
          Container(
            margin: const EdgeInsets.only(right: 16),
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              decoration: BoxDecoration(
                border: Border.all(color: Colors.pink),
                borderRadius: BorderRadius.circular(16),
              ),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(Icons.tune, color: Colors.pink, size: 16),
                  const SizedBox(width: 4),
                  Text(
                    'Filters',
                    style: TextStyle(
                      color: Colors.pink,
                      fontSize: 12,
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
      body: Column(
        children: [
          // Search Bar
          Container(
            margin: const EdgeInsets.all(16),
            child: TextField(
              decoration: InputDecoration(
                hintText: 'Search orders',
                hintStyle: TextStyle(color: Colors.grey[500]),
                prefixIcon: Icon(Icons.search, color: Colors.grey[500]),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(8),
                  borderSide: BorderSide(color: Colors.grey[300]!),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(8),
                  borderSide: BorderSide(color: Colors.grey[300]!),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(8),
                  borderSide: const BorderSide(color: Colors.pink),
                ),
                contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              ),
            ),
          ),
          // Orders List
          Expanded(
            child: _isLoading
                ? _buildShimmerList()
                : _orders.isEmpty
                    ? _buildEmptyState()
                    : RefreshIndicator(
                        onRefresh: _loadOrders,
                        child: ListView.builder(
                          padding: const EdgeInsets.symmetric(horizontal: 16),
                          itemCount: _orders.length,
                          itemBuilder: (context, index) {
                            final order = _orders[index];
                            return _buildOrderCard(order);
                          },
                        ),
                      ),
          ),
          if (!_isLoading && _orders.isNotEmpty)
            Container(
              padding: const EdgeInsets.all(32),
              child: Column(
                children: [
                  Container(
                    width: 120,
                    height: 120,
                    decoration: BoxDecoration(
                      color: Colors.purple[100],
                      borderRadius: BorderRadius.circular(60),
                    ),
                    child: Icon(
                      Icons.shopping_bag_outlined,
                      size: 60,
                      color: Colors.purple[400],
                    ),
                  ),
                  const SizedBox(height: 16),
                  const Text(
                    'All Izzz Well',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w600,
                      color: Colors.grey,
                    ),
                  ),
                  const SizedBox(height: 4),
                  const Text(
                    'Just keep shopping',
                    style: TextStyle(
                      fontSize: 14,
                      color: Colors.grey,
                      fontStyle: FontStyle.italic,
                    ),
                  ),
                ],
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildOrderCard(Map<String, dynamic> order) {
    // Safely get values with null checks
    final status = order['status']?.toString() ?? 'Unknown Status';
    final date = order['date']?.toString() ?? order['created_at']?.toString() ?? 'Unknown Date';
    final productName = order['product_name']?.toString() ?? 
                       order['name']?.toString() ?? 
                       'Product Name';
    final size = order['size']?.toString() ?? 'N/A';
    final quantity = order['quantity']?.toString() ?? '1';
    final image = order['image']?.toString() ?? '';
    final totalAmount = order['total_amount']?.toString() ?? 
                       order['total']?.toString() ?? 
                       '₹0';

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(8),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.1),
            spreadRadius: 1,
            blurRadius: 4,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          children: [
            // Product Image
            Container(
              width: 80,
              height: 80,
              decoration: BoxDecoration(
                color: Colors.grey[100],
                borderRadius: BorderRadius.circular(8),
              ),
              child: ClipRRect(
                borderRadius: BorderRadius.circular(8),
                child: image.isNotEmpty
                    ? Image.network(
                        image,
                        fit: BoxFit.cover,
                        errorBuilder: (context, error, stackTrace) {
                          return Icon(Icons.shopping_bag, color: Colors.grey[400], size: 40);
                        },
                      )
                    : Icon(Icons.shopping_bag, color: Colors.grey[400], size: 40),
              ),
            ),
            const SizedBox(width: 16),
            // Order Details
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    status,
                    style: TextStyle(
                      fontSize: 14,
                      fontWeight: FontWeight.w600,
                      color: status.toLowerCase().contains('cancelled') 
                          ? Colors.red 
                          : status.toLowerCase().contains('delivered')
                              ? Colors.green
                              : Colors.blue,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    date,
                    style: TextStyle(
                      fontSize: 12,
                      color: Colors.grey[600],
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    productName,
                    style: const TextStyle(
                      fontSize: 13,
                      fontWeight: FontWeight.w500,
                      color: Colors.black87,
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 4),
                  Row(
                    children: [
                      Text(
                        'Size: $size • Qty: $quantity',
                        style: TextStyle(
                          fontSize: 11,
                          color: Colors.grey[600],
                        ),
                      ),
                      const Spacer(),
                      Text(
                        totalAmount,
                        style: const TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.w600,
                          color: Colors.black87,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            // Arrow Icon
            Icon(
              Icons.arrow_forward_ios,
              size: 16,
              color: Colors.grey[400],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildShimmerList() {
    return ListView.builder(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      itemCount: 5,
      itemBuilder: (context, index) {
        return Container(
          margin: const EdgeInsets.only(bottom: 16),
          child: Shimmer.fromColors(
            baseColor: Colors.grey[300]!,
            highlightColor: Colors.grey[100]!,
            child: Container(
              height: 100,
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(8),
              ),
            ),
          ),
        );
      },
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(
            Icons.shopping_cart_outlined,
            size: 64,
            color: Colors.grey[400],
          ),
          const SizedBox(height: 16),
          Text(
            'No orders found',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.w600,
              color: Colors.grey[600],
            ),
          ),
          const SizedBox(height: 8),
          Text(
            'Your orders will appear here',
            style: TextStyle(
              fontSize: 14,
              color: Colors.grey[500],
            ),
          ),
        ],
      ),
    );
  }
}
