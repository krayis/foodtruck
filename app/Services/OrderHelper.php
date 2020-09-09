<?php

namespace App\Services;

use App\Modifier;
use App\OrderItemModifier;
use Ramsey\Uuid\Uuid;
use App\Order;
use App\Item;
use App\OrderItem;

class OrderHelper
{
    public static function create($request)
    {
        $cart = $request->input('cart');
        $subTotal = 0;

        $itemIds = [];
        foreach ($cart as $item) {
            array_push($itemIds, $item['id']);
        }

        $items = Item::where([
            ['active', 1],
            ['deleted', 0],
            ['truck_id', $request->input('truck.id')]
        ])->whereIn('id', $itemIds)->get();

        $modifierIds = [];
        foreach ($cart as $row) {
            $item = $items->find($row['id']);
            $subTotal += $item->price;
            foreach ($row['modifiers'] as $modifier) {
                array_push($modifierIds, $modifier['id']);
            }
        }

        $modifiers = Modifier::where([
            ['active', 1],
            ['deleted', 0],
        ])->whereIn('id', $modifierIds)->get();
        foreach ($cart as $row) {
            foreach ($row['modifiers'] as $modifier) {
                $modifier = $modifiers->find($modifier['id']);
                $subTotal += $modifier->price;
            }
        }
        $tax = 7;
        $taxRate = $tax / 100;
        $taxTotal = $subTotal * $taxRate;
        $grandTotal = $taxTotal + $subTotal;

        $uuid = Uuid::uuid4();

        $order = Order::create([
            'truck_id' => $request->input('truck.id'),
            'event_id' => $request->input('event.id'),
            'uuid' => $uuid->toString(),
            'tip' => 0,
            'tax' => $tax,
            'sub_total' => $subTotal,
            'tax_total' => $taxTotal,
            'grand_total' => $grandTotal,
            'status' => Order::STATUS_CREATED,
        ]);

        foreach ($cart as $row) {
            $item = $items->find($row['id']);
            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'item_id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'price' => $item->price,
            ]);
            foreach ($row['modifiers'] as $modifier) {
                $modifier = $modifiers->find($modifier['id']);
                OrderItemModifier::create([
                    'order_id' => $order->id,
                    'order_item_id' => $orderItem->id,
                    'name' => $modifier->name,
                    'price' => $modifier->price,
                ]);
            }
        }

        return $order;
    }

    public static function updateGrandTotal($order) {
        $subTotal = 0;
        foreach ($order->items as $item) {
            $subTotal += $item->price;
            foreach ($item->modifiers as $modifier) {
                $subTotal += $modifier->price;
            }
        }

        $tax = $order->tax;
        $taxRate = $tax / 100;
        $taxTotal = $subTotal * $taxRate;
        $tip = $order->tip;
        $grandTotal = $taxTotal + $subTotal + $tip;

        $order = $order->update([
            'tax' => $tax,
            'sub_total' => $subTotal,
            'tax_total' => $taxTotal,
            'grand_total' => $grandTotal,
        ]);

        return $order;
    }
}
