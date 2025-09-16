import 'package:json_annotation/json_annotation.dart';

part 'product.g.dart';

@JsonSerializable()
class Product {
  final int id;
  @JsonKey(name: 'category_id')
  final int categoryId;
  final String name;
  final String slug;
  final String description;
  @JsonKey(fromJson: _parsePrice)
  final double price;
  @JsonKey(name: 'discount_price', fromJson: _parseDiscountPrice)
  final double? discountPrice;
  final int stock;
  final String sku;
  final String? image;
  @JsonKey(name: 'is_featured')
  final bool isFeatured;
  final bool status;
  @JsonKey(name: 'created_at')
  final String createdAt;
  @JsonKey(name: 'updated_at')
  final String updatedAt;

  Product({
    required this.id,
    required this.categoryId,
    required this.name,
    required this.slug,
    required this.description,
    required this.price,
    this.discountPrice,
    required this.stock,
    required this.sku,
    this.image,
    required this.isFeatured,
    required this.status,
    required this.createdAt,
    required this.updatedAt,
  });

  factory Product.fromJson(Map<String, dynamic> json) => _$ProductFromJson(json);
  Map<String, dynamic> toJson() => _$ProductToJson(this);
}

// Helper functions to parse string prices to double
double _parsePrice(dynamic value) {
  if (value is String) {
    return double.tryParse(value) ?? 0.0;
  }
  if (value is num) {
    return value.toDouble();
  }
  return 0.0;
}

double? _parseDiscountPrice(dynamic value) {
  if (value == null) return null;
  if (value is String) {
    return double.tryParse(value);
  }
  if (value is num) {
    return value.toDouble();
  }
  return null;
}

@JsonSerializable()
class Category {
  final int id;
  final String name;
  final String slug;
  final String? description;
  final String? image;
  @JsonKey(name: 'is_active')
  final bool isActive;
  @JsonKey(name: 'sort_order')
  final int sortOrder;
  @JsonKey(name: 'created_at')
  final String createdAt;
  @JsonKey(name: 'updated_at')
  final String updatedAt;

  Category({
    required this.id,
    required this.name,
    required this.slug,
    this.description,
    this.image,
    required this.isActive,
    required this.sortOrder,
    required this.createdAt,
    required this.updatedAt,
  });

  factory Category.fromJson(Map<String, dynamic> json) => _$CategoryFromJson(json);
  Map<String, dynamic> toJson() => _$CategoryToJson(this);
}
