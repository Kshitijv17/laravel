import 'package:json_annotation/json_annotation.dart';

part 'order.g.dart';

@JsonSerializable()
class Order {
  final int id;
  @JsonKey(name: 'user_id')
  final int userId;
  @JsonKey(name: 'order_number')
  final String orderNumber;
  final String status;
  @JsonKey(name: 'total_amount')
  final double totalAmount;
  @JsonKey(name: 'shipping_address')
  final String? shippingAddress;
  @JsonKey(name: 'billing_address')
  final String? billingAddress;
  @JsonKey(name: 'payment_method')
  final String? paymentMethod;
  @JsonKey(name: 'payment_status')
  final String paymentStatus;
  final String? notes;
  @JsonKey(name: 'created_at')
  final String createdAt;
  @JsonKey(name: 'updated_at')
  final String updatedAt;

  Order({
    required this.id,
    required this.userId,
    required this.orderNumber,
    required this.status,
    required this.totalAmount,
    this.shippingAddress,
    this.billingAddress,
    this.paymentMethod,
    required this.paymentStatus,
    this.notes,
    required this.createdAt,
    required this.updatedAt,
  });

  factory Order.fromJson(Map<String, dynamic> json) => _$OrderFromJson(json);
  Map<String, dynamic> toJson() => _$OrderToJson(this);
}

@JsonSerializable()
class DashboardStats {
  @JsonKey(name: 'total_orders')
  final int totalOrders;
  @JsonKey(name: 'total_products')
  final int totalProducts;
  @JsonKey(name: 'total_customers')
  final int totalCustomers;
  @JsonKey(name: 'total_revenue')
  final double totalRevenue;
  @JsonKey(name: 'pending_orders')
  final int pendingOrders;
  @JsonKey(name: 'recent_orders')
  final List<Order> recentOrders;

  DashboardStats({
    required this.totalOrders,
    required this.totalProducts,
    required this.totalCustomers,
    required this.totalRevenue,
    required this.pendingOrders,
    required this.recentOrders,
  });

  factory DashboardStats.fromJson(Map<String, dynamic> json) => _$DashboardStatsFromJson(json);
  Map<String, dynamic> toJson() => _$DashboardStatsToJson(this);
}
