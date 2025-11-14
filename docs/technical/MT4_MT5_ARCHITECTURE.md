# MT4 and MT5 Trading Architecture - Complete Reference

## Executive Summary

This document provides a comprehensive understanding of MetaTrader 4 (MT4) and MetaTrader 5 (MT5) trading architectures, focusing on the critical differences in how trades, positions, orders, and deals are structured and managed.

## Critical Architectural Differences

### MT4 Architecture (Order-Based System)

**Core Concept:** MT4 uses an **order-based** system where each order is independent.

#### Key Characteristics:
- **Multiple positions per symbol**: You can have multiple BUY and SELL orders on the same symbol simultaneously
- **Each order is independent**: Order ticket = Position ticket
- **Hedging by default**: Allows opposite positions on the same symbol
- **No position netting**: Each order creates a separate position

#### MT4 Data Structure:
```
Order (ticket: 12345)
├── Type: OP_BUY (0), OP_SELL (1), OP_BUYLIMIT (2), OP_SELLLIMIT (3), OP_BUYSTOP (4), OP_SELLSTOP (5)
├── Symbol: EURUSD
├── Volume: 1.0 lot
├── Open Price: 1.1000
├── Close Price: 1.1050
├── Open Time: 2024-01-01 10:00:00
├── Close Time: 2024-01-01 15:00:00
├── Profit: +50.00 USD
├── Commission: -5.00 USD
├── Swap: -2.00 USD
└── Magic Number: 123456
```

**Important:** In MT4, when you close an order, it moves to history with both open and close times recorded.

---

### MT5 Architecture (Position-Based System)

**Core Concept:** MT5 uses a **position-based** system with netting and hedging modes.

#### Three Core Entities:

### 1. Orders (Trade Requests)
Orders are **requests** to execute trading operations sent to the trading server.

**Types:**
- **Market Orders**: `ORDER_TYPE_BUY`, `ORDER_TYPE_SELL` (immediate execution)
- **Pending Orders**: `ORDER_TYPE_BUY_LIMIT`, `ORDER_TYPE_SELL_LIMIT`, `ORDER_TYPE_BUY_STOP`, `ORDER_TYPE_SELL_STOP`, `ORDER_TYPE_BUY_STOP_LIMIT`, `ORDER_TYPE_SELL_STOP_LIMIT`
- **Special**: `ORDER_TYPE_CLOSE_BY` (close by opposite position)

**Order States:**
- `ORDER_STATE_STARTED` - Checked but not accepted
- `ORDER_STATE_PLACED` - Accepted by broker
- `ORDER_STATE_PARTIAL` - Partially executed
- `ORDER_STATE_FILLED` - Fully executed
- `ORDER_STATE_CANCELED` - Canceled by client
- `ORDER_STATE_REJECTED` - Rejected by broker
- `ORDER_STATE_EXPIRED` - Expired

**Key Properties:**
- `ORDER_TICKET` - Unique order ticket
- `ORDER_POSITION_ID` - Position identifier (set when order is executed)
- `ORDER_MAGIC` - Expert Advisor magic number
- `ORDER_TIME_SETUP` - Order placement time
- `ORDER_TIME_DONE` - Execution/cancellation time
- `ORDER_VOLUME_INITIAL` - Initial volume
- `ORDER_VOLUME_CURRENT` - Current volume (for partial fills)

### 2. Deals (Execution Results)
Deals are the **result of order execution** - the actual trade transactions.

**Critical Understanding:**
- Each deal is based on ONE order
- ONE order can generate MULTIPLE deals (partial fills)
- Deals are **immutable** - stored in history forever
- Deals cannot be modified or deleted

**Deal Types:**
- `DEAL_TYPE_BUY` - Buy deal
- `DEAL_TYPE_SELL` - Sell deal
- `DEAL_TYPE_BALANCE` - Balance operation
- `DEAL_TYPE_CREDIT` - Credit operation
- `DEAL_TYPE_COMMISSION` - Commission charge
- `DEAL_TYPE_INTEREST` - Interest rate
- And many more non-trading types...

**Deal Entry Types (CRITICAL):**
- `DEAL_ENTRY_IN` - **Entry into market** (opening position or adding to it)
- `DEAL_ENTRY_OUT` - **Exit from market** (closing position or reducing it)
- `DEAL_ENTRY_INOUT` - **Reverse** (closing and opening opposite position in one action)
- `DEAL_ENTRY_OUT_BY` - **Close by opposite** (hedging mode only)

**Key Properties:**
- `DEAL_TICKET` - Unique deal ticket
- `DEAL_ORDER` - Order ticket that generated this deal
- `DEAL_POSITION_ID` - **Position identifier** (links deal to position)
- `DEAL_ENTRY` - Entry type (IN/OUT/INOUT/OUT_BY)
- `DEAL_TIME` - Deal execution time
- `DEAL_VOLUME` - Deal volume
- `DEAL_PRICE` - Deal execution price
- `DEAL_PROFIT` - Deal profit/loss
- `DEAL_COMMISSION` - Deal commission
- `DEAL_SWAP` - Cumulative swap on close
- `DEAL_MAGIC` - Magic number

**Deal Reason:**
- `DEAL_REASON_CLIENT` - Desktop terminal
- `DEAL_REASON_MOBILE` - Mobile app
- `DEAL_REASON_WEB` - Web platform
- `DEAL_REASON_EXPERT` - Expert Advisor/Script
- `DEAL_REASON_SL` - Stop Loss triggered
- `DEAL_REASON_TP` - Take Profit triggered
- `DEAL_REASON_SO` - Stop Out
- `DEAL_REASON_ROLLOVER` - Rollover
- `DEAL_REASON_VMARGIN` - Variation margin

### 3. Positions (Current Market Exposure)
Positions represent **current open contracts** on financial instruments.

**Critical Rules:**
- **Netting Mode**: ONE position per symbol (default for most brokers)
  - New BUY on existing BUY position → Increases position volume
  - New SELL on existing BUY position → Decreases position volume (or reverses if larger)
  - Position closes when volume reaches zero
  
- **Hedging Mode**: MULTIPLE positions per symbol allowed
  - Each position has unique ticket
  - Can have simultaneous BUY and SELL positions on same symbol
  - Similar to MT4 behavior

**Key Properties:**
- `POSITION_TICKET` - Position ticket (can change due to server operations like swap charging)
- `POSITION_IDENTIFIER` - **Unique permanent identifier** (NEVER changes during position lifetime)
- `POSITION_SYMBOL` - Trading symbol
- `POSITION_TYPE` - `POSITION_TYPE_BUY` or `POSITION_TYPE_SELL`
- `POSITION_VOLUME` - Current position volume
- `POSITION_PRICE_OPEN` - Position open price
- `POSITION_PRICE_CURRENT` - Current symbol price
- `POSITION_SL` - Stop Loss level
- `POSITION_TP` - Take Profit level
- `POSITION_PROFIT` - Current profit/loss
- `POSITION_SWAP` - Cumulative swap
- `POSITION_TIME` - Position open time
- `POSITION_TIME_UPDATE` - Last modification time
- `POSITION_MAGIC` - Magic number

**Position Lifecycle:**
1. Order placed → Order executed → Deal created with `DEAL_ENTRY_IN`
2. Position opened/modified
3. Opposite order placed → Order executed → Deal created with `DEAL_ENTRY_OUT`
4. Position closed/reduced
5. Position moves to history (accessible via deals)

---

## The Relationship Between Orders, Deals, and Positions

### Example Flow (Netting Mode):

```
Step 1: Open Position
Order #1001 (BUY 1.0 lot EURUSD)
  ↓ (executed)
Deal #2001 (DEAL_ENTRY_IN, volume: 1.0, price: 1.1000)
  ↓ (creates)
Position #3001 (POSITION_IDENTIFIER: 3001, volume: 1.0, type: BUY)

Step 2: Add to Position
Order #1002 (BUY 0.5 lot EURUSD)
  ↓ (executed)
Deal #2002 (DEAL_ENTRY_IN, volume: 0.5, price: 1.1010, DEAL_POSITION_ID: 3001)
  ↓ (modifies)
Position #3001 (volume: 1.5, type: BUY) - SAME POSITION_IDENTIFIER

Step 3: Partially Close Position
Order #1003 (SELL 0.5 lot EURUSD)
  ↓ (executed)
Deal #2003 (DEAL_ENTRY_OUT, volume: 0.5, price: 1.1050, DEAL_POSITION_ID: 3001, profit: +20.00)
  ↓ (modifies)
Position #3001 (volume: 1.0, type: BUY) - SAME POSITION_IDENTIFIER

Step 4: Close Position
Order #1004 (SELL 1.0 lot EURUSD)
  ↓ (executed)
Deal #2004 (DEAL_ENTRY_OUT, volume: 1.0, price: 1.1060, DEAL_POSITION_ID: 3001, profit: +60.00)
  ↓ (closes)
Position #3001 CLOSED (removed from active positions, accessible via history)
```

**Key Insight:** All deals (#2001, #2002, #2003, #2004) have the SAME `DEAL_POSITION_ID: 3001`, linking them to the same position lifecycle.

---

## POSITION_IDENTIFIER: The Master Key

### What is POSITION_IDENTIFIER?

`POSITION_IDENTIFIER` is a **unique permanent number** assigned to each position that:
- **NEVER changes** during the position's entire lifetime
- Remains the same even if `POSITION_TICKET` changes (due to server operations)
- Is assigned to ALL orders and deals related to that position
- Survives position reversals in netting mode
- Is the PRIMARY KEY for tracking position history

### How to Use POSITION_IDENTIFIER:

```php
// Get current position identifier
$positionId = $position->identifier; // e.g., 1575550193

// Find ALL deals related to this position
$deals = Deal::where('position_id', $positionId)
    ->orderBy('time', 'asc')
    ->get();

// Separate IN and OUT deals
$inDeals = $deals->where('entry', 'in');   // Position opens/additions
$outDeals = $deals->where('entry', 'out'); // Position closes/reductions

// Calculate position lifetime
$openTime = $inDeals->first()->time;
$closeTime = $outDeals->last()->time;
$holdTime = $closeTime->diffInSeconds($openTime);

// Calculate total profit
$totalProfit = $outDeals->sum('profit');
```

---

## Critical Differences Summary

| Aspect | MT4 | MT5 |
|--------|-----|-----|
| **Core Model** | Order-based | Position-based |
| **Positions per Symbol** | Multiple (hedging) | One (netting) or Multiple (hedging mode) |
| **Order = Position?** | Yes (ticket matches) | No (separate entities) |
| **History Tracking** | Orders with open/close times | Deals with entry types (IN/OUT) |
| **Unique Identifier** | Order ticket | POSITION_IDENTIFIER |
| **Partial Fills** | Not common | Common (multiple deals per order) |
| **Position Modification** | New order = new position | New order = modify existing position |
| **Closed Trade Data** | Order history | Deal history with DEAL_ENTRY_OUT |

---

## How to Query Trading Data Correctly

### For MT5 (Position-Based):

#### Get All Closed Trades:
```php
// CORRECT: Use deals with entry='out'
$closedTrades = Deal::where('account_id', $accountId)
    ->where('entry', 'out')
    ->where('type', 'in', ['buy', 'sell']) // Exclude balance/credit operations
    ->orderBy('time', 'desc')
    ->get();
```

#### Get Position History:
```php
// Get all deals for a specific position
$positionDeals = Deal::where('position_id', $positionId)
    ->orderBy('time', 'asc')
    ->get();

// Calculate position metrics
$inDeals = $positionDeals->where('entry', 'in');
$outDeals = $positionDeals->where('entry', 'out');

$openTime = $inDeals->first()->time;
$closeTime = $outDeals->last()->time;
$totalVolume = $inDeals->sum('volume');
$totalProfit = $outDeals->sum('profit');
$totalCommission = $positionDeals->sum('commission');
$totalSwap = $outDeals->last()->swap; // Cumulative swap on last OUT deal
```

#### Get Open Positions:
```php
// Use the positions table (current state only)
$openPositions = Position::where('account_id', $accountId)
    ->get();
```

### For MT4 (Order-Based):

#### Get All Closed Trades:
```php
// Use orders with close_time not null
$closedTrades = Order::where('account_id', $accountId)
    ->whereNotNull('close_time')
    ->where('type', 'in', [0, 1]) // OP_BUY=0, OP_SELL=1
    ->orderBy('close_time', 'desc')
    ->get();
```

---

## Common Mistakes to Avoid

### ❌ WRONG: Using Positions table for historical analysis
```php
// This only shows CURRENT open positions (~43 records)
$trades = Position::where('account_id', $accountId)->get();
```

### ✅ CORRECT: Using Deals table with entry='out' for closed trades
```php
// This shows ALL closed trades (313+ records)
$trades = Deal::where('account_id', $accountId)
    ->where('entry', 'out')
    ->where('type', 'in', ['buy', 'sell'])
    ->get();
```

### ❌ WRONG: Grouping by deal ticket
```php
// Deal tickets are unique per deal, not per position
$grouped = Deal::groupBy('ticket')->get();
```

### ✅ CORRECT: Grouping by position_id
```php
// Position ID links all deals of the same position
$grouped = Deal::groupBy('position_id')->get();
```

### ❌ WRONG: Calculating hold time from single deal
```php
// A deal only has execution time, not open/close times
$holdTime = $deal->time->diffInSeconds($someOtherTime);
```

### ✅ CORRECT: Calculating hold time from IN and OUT deals
```php
$inDeal = Deal::where('position_id', $positionId)
    ->where('entry', 'in')
    ->first();
    
$outDeal = Deal::where('position_id', $positionId)
    ->where('entry', 'out')
    ->orderBy('time', 'desc')
    ->first();
    
$holdTime = $outDeal->time->diffInSeconds($inDeal->time);
```

---

## Database Schema Implications

### Deals Table (MT5):
```sql
deals
├── id (primary key)
├── account_id (foreign key)
├── ticket (unique deal ticket)
├── order (order ticket that created this deal)
├── position_id (POSITION_IDENTIFIER - links to position)
├── time (deal execution time)
├── type (buy/sell/balance/credit/etc)
├── entry (in/out/inout/out_by) ← CRITICAL
├── volume
├── price
├── profit
├── commission
├── swap (cumulative on OUT deals)
├── symbol
├── magic
└── comment
```

### Positions Table (MT5):
```sql
positions
├── id (primary key)
├── account_id (foreign key)
├── ticket (position ticket - can change)
├── identifier (POSITION_IDENTIFIER - permanent) ← CRITICAL
├── time (open time)
├── time_update (last update time)
├── type (buy/sell)
├── volume (current volume)
├── price_open
├── price_current
├── sl
├── tp
├── profit (current unrealized profit)
├── swap (cumulative swap)
├── symbol
├── magic
└── comment
```

**Note:** Positions table only contains CURRENTLY OPEN positions. For historical positions, query Deals table by position_id.

---

## Performance Considerations

### History Loading in MT5:

MT5 uses a **cache-based system** for historical data:

1. **HistorySelect(start, end)** - Loads history for time range
2. **HistorySelectByPosition(position_id)** - Loads all orders/deals for specific position
3. **HistoryDealSelect(ticket)** - Loads single deal by ticket

**Warning:** Loading entire history can consume significant memory:
- 134,502 deals + 218,740 orders = ~363 MB RAM usage
- Always limit time ranges in queries
- Use pagination for large datasets

### Query Optimization:

```php
// ❌ BAD: Load all deals then filter
$deals = Deal::all()->where('entry', 'out');

// ✅ GOOD: Filter at database level with limits
$deals = Deal::where('entry', 'out')
    ->where('account_id', $accountId)
    ->whereBetween('time', [$startDate, $endDate])
    ->limit(1000)
    ->get();
```

---

## Practical Examples

### Example 1: Calculate Win Rate
```php
$closedDeals = Deal::where('account_id', $accountId)
    ->where('entry', 'out')
    ->where('type', 'in', ['buy', 'sell'])
    ->get();

$winningTrades = $closedDeals->where('profit', '>', 0)->count();
$totalTrades = $closedDeals->count();
$winRate = $totalTrades > 0 ? ($winningTrades / $totalTrades) * 100 : 0;
```

### Example 2: Calculate Average Hold Time
```php
$positions = Deal::where('account_id', $accountId)
    ->where('entry', 'out')
    ->where('type', 'in', ['buy', 'sell'])
    ->get()
    ->groupBy('position_id');

$holdTimes = [];
foreach ($positions as $positionId => $deals) {
    $inDeal = Deal::where('position_id', $positionId)
        ->where('entry', 'in')
        ->first();
    
    $outDeal = $deals->last(); // Last OUT deal
    
    if ($inDeal && $outDeal) {
        $holdTimes[] = $outDeal->time->diffInSeconds($inDeal->time);
    }
}

$avgHoldTime = count($holdTimes) > 0 ? array_sum($holdTimes) / count($holdTimes) : 0;
```

### Example 3: Most Profitable Symbol
```php
$symbolProfits = Deal::where('account_id', $accountId)
    ->where('entry', 'out')
    ->where('type', 'in', ['buy', 'sell'])
    ->groupBy('symbol')
    ->selectRaw('symbol, SUM(profit) as total_profit, COUNT(*) as trade_count')
    ->orderBy('total_profit', 'desc')
    ->limit(10)
    ->get();
```

---

## References

- [Orders, Positions and Deals in MetaTrader 5](https://www.mql5.com/en/articles/211)
- [Position Properties - MQL5 Reference](https://www.mql5.com/en/docs/constants/tradingconstants/positionproperties)
- [Deal Properties - MQL5 Reference](https://www.mql5.com/en/docs/constants/tradingconstants/dealproperties)
- [Order Properties - MQL5 Reference](https://www.mql5.com/en/docs/constants/tradingconstants/orderproperties)
- [MT4 Order Properties](https://docs.mql4.com/constants/tradingconstants/orderproperties)

---

## 👨‍💻 Author & Contact

**Ruslan Abuzant**  
📧 Email: [ruslan@abuzant.com](mailto:ruslan@abuzant.com)  
🌐 Website: [https://abuzant.com](https://abuzant.com)  
💼 LinkedIn: [linkedin.com/in/ruslanabuzant](https://linkedin.com/in/ruslanabuzant)  
❤️ From Palestine to the world with Love

For project support and inquiries:  
📧 [hello@thetradevisor.com](mailto:hello@thetradevisor.com)  
🌐 [https://thetradevisor.com](https://thetradevisor.com)
