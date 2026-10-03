<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order Notification - Kingsman</title>
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        body { margin: 0; padding: 0; width: 100% !important; background-color: #f4f6f8; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
    </style>
</head>
<body style="margin: 0; padding: 20px 0; background-color: #f4f6f8;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 10px 15px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 620px; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
                    
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding: 24px 20px; background-color: #0b0b0c; border-bottom: 3px solid #c5a880;">
                            <h1 style="margin: 0; font-size: 24px; font-weight: 800; color: #ffffff; letter-spacing: 2px; text-transform: uppercase;">
                                KINGSMAN
                            </h1>
                            <p style="margin: 4px 0 0 0; font-size: 11px; color: #c5a880; letter-spacing: 1.5px; text-transform: uppercase; font-weight: 600;">
                                Luxury Ethnic & Bespoke Menswear
                            </p>
                        </td>
                    </tr>

                    <!-- Alert Banner -->
                    <tr>
                        <td style="padding: 16px 24px; background-color: #ecfdf5; border-bottom: 1px solid #a7f3d0;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="vertical-align: middle;">
                                        <span style="display: inline-block; background-color: #059669; color: #ffffff; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.5px;">
                                            New Web Order
                                        </span>
                                        <h2 style="margin: 6px 0 2px 0; font-size: 17px; font-weight: 700; color: #065f46;">
                                            Order #{{ $order['order_id'] ?? ($order['invoice_no'] ?? 'N/A') }} Received!
                                        </h2>
                                        <p style="margin: 0; font-size: 12px; color: #047857;">
                                            A new order has been placed on your website on <strong>{{ $order['created_at'] ?? date('d M Y, h:i A') }}</strong>.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Customer & Delivery Summary -->
                    <tr>
                        <td style="padding: 24px 24px 16px 24px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <!-- Customer Details -->
                                    <td width="50%" style="vertical-align: top; padding-right: 12px;">
                                        <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px;">
                                            <h3 style="margin: 0 0 8px 0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #475569; letter-spacing: 0.5px;">
                                                👤 Customer Info
                                            </h3>
                                            <p style="margin: 0 0 4px 0; font-size: 14px; font-weight: 700; color: #0f172a;">
                                                {{ $order['customer_name'] ?? 'N/A' }}
                                            </p>
                                            <p style="margin: 0 0 4px 0; font-size: 13px; color: #1e293b;">
                                                <strong>Phone:</strong> <a href="tel:{{ $order['phone'] ?? '' }}" style="color: #2563eb; text-decoration: none; font-weight: 600;">{{ $order['phone'] ?? 'N/A' }}</a>
                                            </p>
                                            @if(!empty($order['email']))
                                                <p style="margin: 0; font-size: 12px; color: #64748b;">
                                                    <strong>Email:</strong> {{ $order['email'] }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Delivery Info -->
                                    <td width="50%" style="vertical-align: top; padding-left: 12px;">
                                        <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px;">
                                            <h3 style="margin: 0 0 8px 0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #475569; letter-spacing: 0.5px;">
                                                📍 Delivery & Payment
                                            </h3>
                                            <p style="margin: 0 0 4px 0; font-size: 13px; color: #1e293b;">
                                                <strong>Address:</strong> {{ $order['address'] ?? 'N/A' }}
                                            </p>
                                            <p style="margin: 0 0 4px 0; font-size: 12px; color: #475569;">
                                                <strong>Zone:</strong> {{ ucwords(str_replace('_', ' ', $order['delivery_zone'] ?? 'Dhaka')) }}
                                            </p>
                                            <p style="margin: 0; font-size: 12px; color: #475569;">
                                                <strong>Payment:</strong> <span style="background-color: #fef3c7; color: #92400e; padding: 1px 6px; border-radius: 3px; font-weight: 600;">{{ ucwords(str_replace('_', ' ', $order['payment_method'] ?? 'Cash on Delivery')) }}</span>
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            @if(!empty($order['notes']))
                                <div style="margin-top: 14px; background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; padding: 10px 14px;">
                                    <p style="margin: 0; font-size: 12px; color: #92400e;">
                                        <strong>📝 Customer Notes:</strong> {{ $order['notes'] }}
                                    </p>
                                </div>
                            @endif
                        </td>
                    </tr>

                    <!-- Order Items Table -->
                    <tr>
                        <td style="padding: 0 24px 20px 24px;">
                            <h3 style="margin: 0 0 10px 0; font-size: 13px; font-weight: 700; text-transform: uppercase; color: #0f172a; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px;">
                                🛍️ Ordered Items ({{ count($items ?? ($order['items'] ?? [])) }})
                            </h3>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                                <thead>
                                    <tr style="background-color: #f1f5f9; border-bottom: 1px solid #cbd5e1;">
                                        <th align="left" style="padding: 8px 10px; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase;">Item</th>
                                        <th align="center" style="padding: 8px 6px; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase;">Size/Color</th>
                                        <th align="center" style="padding: 8px 6px; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase;">Qty</th>
                                        <th align="right" style="padding: 8px 6px; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase;">Price</th>
                                        <th align="right" style="padding: 8px 10px; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $itemList = !empty($items) ? $items : ($order['items'] ?? []);
                                    @endphp
                                    @foreach($itemList as $item)
                                        @php
                                            $qty = (float)($item['quantity'] ?? $item['qty'] ?? 1);
                                            $price = (float)($item['price'] ?? 0);
                                            $sub = $qty * $price;
                                        @endphp
                                        <tr style="border-bottom: 1px solid #e2e8f0;">
                                            <td style="padding: 10px 10px; font-size: 13px; color: #0f172a; font-weight: 600;">
                                                {{ $item['name'] ?? 'Outfit' }}
                                            </td>
                                            <td align="center" style="padding: 10px 6px; font-size: 12px; color: #475569;">
                                                {{ $item['size'] ?? 'L' }} / {{ $item['color'] ?? 'Default' }}
                                            </td>
                                            <td align="center" style="padding: 10px 6px; font-size: 13px; color: #0f172a; font-weight: 700;">
                                                {{ (int)$qty }}
                                            </td>
                                            <td align="right" style="padding: 10px 6px; font-size: 13px; color: #475569;">
                                                ৳{{ number_format($price) }}
                                            </td>
                                            <td align="right" style="padding: 10px 10px; font-size: 13px; color: #0f172a; font-weight: 700;">
                                                ৳{{ number_format($sub) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    <!-- Total Calculation Box -->
                    <tr>
                        <td style="padding: 0 24px 24px 24px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px 16px;">
                                <tr>
                                    <td style="padding: 4px 0; font-size: 13px; color: #64748b;">Subtotal:</td>
                                    <td align="right" style="padding: 4px 0; font-size: 13px; color: #0f172a; font-weight: 600;">৳{{ number_format((float)($order['subtotal'] ?? 0)) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px 0; font-size: 13px; color: #64748b;">Delivery Fee:</td>
                                    <td align="right" style="padding: 4px 0; font-size: 13px; color: #0f172a; font-weight: 600;">৳{{ number_format((float)($order['shipping_charge'] ?? 0)) }}</td>
                                </tr>
                                @if(!empty($order['discount']) && $order['discount'] > 0)
                                <tr>
                                    <td style="padding: 4px 0; font-size: 13px; color: #dc2626;">Discount:</td>
                                    <td align="right" style="padding: 4px 0; font-size: 13px; color: #dc2626; font-weight: 600;">- ৳{{ number_format((float)$order['discount']) }}</td>
                                </tr>
                                @endif
                                <tr style="border-top: 1px solid #cbd5e1;">
                                    <td style="padding: 10px 0 4px 0; font-size: 16px; font-weight: 800; color: #0f172a;">Grand Total:</td>
                                    <td align="right" style="padding: 10px 0 4px 0; font-size: 18px; font-weight: 800; color: #059669;">৳{{ number_format((float)($order['grand_total'] ?? 0)) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Call To Action -->
                    <tr>
                        <td align="center" style="padding: 0 24px 28px 24px;">
                            <a href="{{ url('/admin/invoice') }}" style="display: inline-block; background-color: #0b0b0c; color: #ffffff; text-decoration: none; padding: 12px 28px; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; border-radius: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                View Order In Admin Panel
                            </a>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding: 16px 20px; background-color: #f1f5f9; border-top: 1px solid #e2e8f0; font-size: 11px; color: #64748b;">
                            <p style="margin: 0 0 4px 0;">
                                This is an automated order notification from <strong>Kingsman Official Online Store</strong>.
                            </p>
                            <p style="margin: 0;">
                                &copy; {{ date('Y') }} Kingsman. All Rights Reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
