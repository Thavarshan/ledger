export type Account = {
    id: number;
    name: string;
    account_type: string;
    account_holder_name: string | null;
    bank_name: string;
    bank_code: string | null;
    branch_name: string | null;
    branch_code: string | null;
    country_code: string;
    currency_code: string;
    account_number_last4: string | null;
    has_iban: boolean;
    swift_bic: string | null;
    has_routing_number: boolean;
    has_sort_code: boolean;
    notes: string | null;
    is_primary: boolean;
    is_active: boolean;
    balance_minor?: string;
    created_at: string | null;
    updated_at: string | null;
};

export type AccountFormData = {
    name: string;
    account_type: string;
    account_holder_name: string;
    bank_name: string;
    bank_code: string;
    branch_name: string;
    branch_code: string;
    country_code: string;
    currency_code: string;
    account_number: string;
    iban: string;
    swift_bic: string;
    routing_number: string;
    sort_code: string;
    notes: string;
    is_primary: boolean;
    is_active: boolean;
};

export type AccountListItem = {
    id: number;
    name: string;
    bank_name: string;
    currency_code: string;
    account_number_last4: string | null;
    is_primary: boolean;
    is_active: boolean;
    balance_minor: string;
};

export type AccountOption = {
    id: number;
    name: string;
    currency_code: string;
};

export type Transaction = {
    id: number;
    account_id: number;
    direction: 'credit' | 'debit';
    amount_minor: string;
    description: string;
    reference: string | null;
    notes: string | null;
    occurred_at: string;
    created_at: string | null;
    updated_at: string | null;
};

export type TransactionWithAccount = Transaction & {
    account: AccountOption;
};

export type TransactionFormData = {
    account_id: string;
    direction: Transaction['direction'];
    amount: string;
    description: string;
    reference: string;
    notes: string;
    occurred_at: string;
};

export type ResourceResponse<T> = {
    data: T;
};

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type Paginated<T> = {
    data: T[];
    meta: {
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: PaginationLink[];
    };
    links: {
        first: string | null;
        last: string | null;
        prev: string | null;
        next: string | null;
    };
};
