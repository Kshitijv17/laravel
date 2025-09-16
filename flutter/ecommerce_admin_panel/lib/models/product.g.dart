// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'product.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

Product _$ProductFromJson(Map<String, dynamic> json) => Product(
  id: (json['id'] as num).toInt(),
  categoryId: (json['category_id'] as num).toInt(),
  name: json['name'] as String,
  slug: json['slug'] as String,
  description: json['description'] as String,
  price: _parsePrice(json['price']),
  discountPrice: _parseDiscountPrice(json['discount_price']),
  stock: (json['stock'] as num).toInt(),
  sku: json['sku'] as String,
  image: json['image'] as String?,
  isFeatured: json['is_featured'] as bool,
  status: json['status'] as bool,
  createdAt: json['created_at'] as String,
  updatedAt: json['updated_at'] as String,
);

Map<String, dynamic> _$ProductToJson(Product instance) => <String, dynamic>{
  'id': instance.id,
  'category_id': instance.categoryId,
  'name': instance.name,
  'slug': instance.slug,
  'description': instance.description,
  'price': instance.price,
  'discount_price': instance.discountPrice,
  'stock': instance.stock,
  'sku': instance.sku,
  'image': instance.image,
  'is_featured': instance.isFeatured,
  'status': instance.status,
  'created_at': instance.createdAt,
  'updated_at': instance.updatedAt,
};

Category _$CategoryFromJson(Map<String, dynamic> json) => Category(
  id: (json['id'] as num).toInt(),
  name: json['name'] as String,
  slug: json['slug'] as String,
  description: json['description'] as String?,
  image: json['image'] as String?,
  isActive: json['is_active'] as bool,
  sortOrder: (json['sort_order'] as num).toInt(),
  createdAt: json['created_at'] as String,
  updatedAt: json['updated_at'] as String,
);

Map<String, dynamic> _$CategoryToJson(Category instance) => <String, dynamic>{
  'id': instance.id,
  'name': instance.name,
  'slug': instance.slug,
  'description': instance.description,
  'image': instance.image,
  'is_active': instance.isActive,
  'sort_order': instance.sortOrder,
  'created_at': instance.createdAt,
  'updated_at': instance.updatedAt,
};
