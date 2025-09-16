// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'order.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

Order _$OrderFromJson(Map<String, dynamic> json) => Order(
  id: (json['id'] as num).toInt(),
  userId: (json['user_id'] as num).toInt(),
  orderNumber: json['order_number'] as String,
  status: json['status'] as String,
  totalAmount: (json['total_amount'] as num).toDouble(),
  shippingAddress: json['shipping_address'] as String?,
  billingAddress: json['billing_address'] as String?,
  paymentMethod: json['payment_method'] as String?,
  paymentStatus: json['payment_status'] as String,
  notes: json['notes'] as String?,
  createdAt: json['created_at'] as String,
  updatedAt: json['updated_at'] as String,
);

Map<String, dynamic> _$OrderToJson(Order instance) => <String, dynamic>{
  'id': instance.id,
  'user_id': instance.userId,
  'order_number': instance.orderNumber,
  'status': instance.status,
  'total_amount': instance.totalAmount,
  'shipping_address': instance.shippingAddress,
  'billing_address': instance.billingAddress,
  'payment_method': instance.paymentMethod,
  'payment_status': instance.paymentStatus,
  'notes': instance.notes,
  'created_at': instance.createdAt,
  'updated_at': instance.updatedAt,
};

DashboardStats _$DashboardStatsFromJson(Map<String, dynamic> json) =>
    DashboardStats(
      totalOrders: (json['total_orders'] as num).toInt(),
      totalProducts: (json['total_products'] as num).toInt(),
      totalCustomers: (json['total_customers'] as num).toInt(),
      totalRevenue: (json['total_revenue'] as num).toDouble(),
      pendingOrders: (json['pending_orders'] as num).toInt(),
      recentOrders: (json['recent_orders'] as List<dynamic>)
          .map((e) => Order.fromJson(e as Map<String, dynamic>))
          .toList(),
    );

Map<String, dynamic> _$DashboardStatsToJson(DashboardStats instance) =>
    <String, dynamic>{
      'total_orders': instance.totalOrders,
      'total_products': instance.totalProducts,
      'total_customers': instance.totalCustomers,
      'total_revenue': instance.totalRevenue,
      'pending_orders': instance.pendingOrders,
      'recent_orders': instance.recentOrders,
    };
