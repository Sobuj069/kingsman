<?php

namespace App\Services;

use Carbon\Carbon;

class ZatcaService
{
    /**
     * Generate ZATCA Phase-1 Compliant Base64 TLV String
     *
     * @param string $sellerName Business / Store Name
     * @param string $vatNumber 15-digit VAT Registration Number
     * @param string|\DateTimeInterface $timestamp Invoice Timestamp
     * @param float|string $totalAmount Invoice Total Amount (inclusive of VAT)
     * @param float|string $vatAmount Total VAT/Tax Amount
     * @return string Base64 encoded TLV string
     */
    public static function generateBase64Qr(
        string $sellerName,
        string $vatNumber,
        $timestamp,
        $totalAmount,
        $vatAmount
    ): string {
        // 1. Format Timestamp to ISO 8601 UTC format (YYYY-MM-DDTHH:mm:ssZ)
        $formattedTimestamp = self::formatTimestamp($timestamp);

        // 2. Format amounts to 2 decimal places
        $formattedTotal = number_format((float) $totalAmount, 2, '.', '');
        $formattedVat   = number_format((float) $vatAmount, 2, '.', '');

        // 3. Build TLV byte string for Tags 1 through 5
        $tlv = self::toTlv(1, $sellerName)
             . self::toTlv(2, $vatNumber)
             . self::toTlv(3, $formattedTimestamp)
             . self::toTlv(4, $formattedTotal)
             . self::toTlv(5, $formattedVat);

        // 4. Return Base64 encoded TLV payload
        return base64_encode($tlv);
    }

    /**
     * Encode Tag, Length, and Value into TLV byte structure.
     *
     * @param int $tag Tag Number (1: Seller, 2: VAT, 3: Time, 4: Total, 5: VAT Amount)
     * @param string $value Value string
     * @return string Binary TLV representation
     */
    private static function toTlv(int $tag, string $value): string
    {
        $valueBytes = $value;
        $length = strlen($valueBytes);

        // Pack Tag (1 byte) + Length (1 byte) + Value (string bytes)
        return pack('C', $tag) . pack('C', $length) . $valueBytes;
    }

    /**
     * Format timestamp into standard ISO 8601 format.
     */
    private static function formatTimestamp($timestamp): string
    {
        if ($timestamp instanceof \DateTimeInterface) {
            return Carbon::instance($timestamp)->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
        }

        try {
            return Carbon::parse($timestamp)->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
        } catch (\Throwable $e) {
            return Carbon::now('UTC')->format('Y-m-d\TH:i:s\Z');
        }
    }
}
