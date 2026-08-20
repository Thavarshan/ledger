import { Head, Link, usePage } from '@inertiajs/react';
import {
    ArrowRight,
    ArrowUpRight,
    BadgeCheck,
    BarChart3,
    CreditCard,
    Landmark,
    LockKeyhole,
    WalletCards,
} from 'lucide-react';
import AppLogoIcon from '@/components/app-logo-icon';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { dashboard, home, login } from '@/routes';

const features = [
    {
        icon: WalletCards,
        title: 'All your accounts, one view',
        description:
            'Keep your cash, savings, and cards together without juggling spreadsheets.',
    },
    {
        icon: ArrowUpRight,
        title: 'Every transaction in context',
        description:
            'Record credits and debits against the account they belong to, in seconds.',
    },
    {
        icon: LockKeyhole,
        title: 'Built for peace of mind',
        description:
            'Your ledger stays private, organized, and ready whenever you need it.',
    },
];

export default function Welcome() {
    const { auth, name } = usePage().props;
    const destination = auth.user ? dashboard() : login();
    const destinationLabel = auth.user ? 'Open dashboard' : 'Start your ledger';

    return (
        <>
            <Head title="Personal finance, simplified" />

            <div className="min-h-screen overflow-hidden bg-background text-foreground">
                <header className="mx-auto flex w-full max-w-7xl items-center justify-between px-5 py-5 sm:px-6 lg:px-8">
                    <Link href={home()} className="flex items-center gap-2.5">
                        <span className="flex size-9 items-center justify-center rounded-xl bg-primary text-primary-foreground shadow-sm shadow-primary/20">
                            <AppLogoIcon className="size-5 invert dark:invert-0" />
                        </span>
                        <span className="text-base font-semibold tracking-tight">
                            {name}
                        </span>
                    </Link>

                    <div className="flex items-center gap-2">
                        {auth.user ? (
                            <Button asChild variant="outline" size="sm">
                                <Link href={dashboard()}>Dashboard</Link>
                            </Button>
                        ) : (
                            <Button asChild variant="ghost" size="sm">
                                <Link href={login()}>Log in</Link>
                            </Button>
                        )}
                        <Button asChild size="sm">
                            <Link href={destination}>{destinationLabel}</Link>
                        </Button>
                    </div>
                </header>

                <main>
                    <section className="relative isolate mx-auto grid max-w-7xl gap-12 px-5 pt-14 pb-20 sm:px-6 lg:grid-cols-[1fr_0.9fr] lg:items-center lg:gap-20 lg:px-8 lg:pt-24 lg:pb-32">
                        <div
                            aria-hidden="true"
                            className="pointer-events-none absolute -top-24 right-0 -z-10 size-[28rem] rounded-full bg-emerald-500/10 blur-3xl dark:bg-emerald-400/10"
                        />
                        <div
                            aria-hidden="true"
                            className="pointer-events-none absolute bottom-0 left-0 -z-10 size-56 rounded-full bg-cyan-500/10 blur-3xl"
                        />
                        <div className="relative z-10 flex flex-col items-start">
                            <Badge
                                variant="secondary"
                                className="gap-1.5 px-3 py-1"
                            >
                                <BadgeCheck className="size-3.5" />
                                Calm, clear personal finance
                            </Badge>
                            <h1 className="mt-6 max-w-2xl text-4xl leading-[1.08] font-semibold tracking-tight text-balance sm:text-5xl lg:text-6xl">
                                Make every rupee feel accounted for.
                            </h1>
                            <p className="mt-6 max-w-lg text-lg leading-8 text-muted-foreground">
                                {name} is a simple home for the accounts and
                                transactions that shape your financial life.
                                Stay organized today and more confident about
                                tomorrow.
                            </p>
                            <div className="mt-8 flex w-full flex-col items-stretch gap-3 sm:w-auto sm:flex-row sm:items-center">
                                <Button asChild size="lg">
                                    <Link href={destination}>
                                        {destinationLabel}
                                        <ArrowRight />
                                    </Link>
                                </Button>
                                {!auth.user && (
                                    <Button asChild variant="outline" size="lg">
                                        <Link href={login()}>
                                            I have an account
                                        </Link>
                                    </Button>
                                )}
                            </div>
                            <p className="mt-5 flex items-center gap-2 text-sm text-muted-foreground">
                                <span className="size-1.5 rounded-full bg-emerald-500" />
                                A focused ledger, without the clutter.
                            </p>
                        </div>

                        <Card className="relative z-10 overflow-hidden border-border/70 bg-card/90 py-0 shadow-2xl shadow-primary/10 backdrop-blur-sm">
                            <CardHeader className="border-b bg-muted/35 px-5 py-4 sm:px-6">
                                <div className="flex items-center justify-between gap-4">
                                    <div>
                                        <CardDescription>
                                            Total balance
                                        </CardDescription>
                                        <CardTitle className="mt-1 text-2xl tracking-tight sm:text-3xl">
                                            LKR 428,750.00
                                        </CardTitle>
                                    </div>
                                    <Badge className="bg-emerald-600 text-white hover:bg-emerald-600 dark:bg-emerald-500 dark:text-emerald-950">
                                        +8.4% this month
                                    </Badge>
                                </div>
                            </CardHeader>
                            <CardContent className="space-y-5 px-5 py-5 sm:px-6 sm:py-6">
                                <div className="grid grid-cols-2 gap-3">
                                    <div className="rounded-lg border bg-background p-3.5">
                                        <p className="text-xs font-medium text-muted-foreground">
                                            Available cash
                                        </p>
                                        <p className="mt-1.5 text-lg font-semibold">
                                            LKR 175,400
                                        </p>
                                    </div>
                                    <div className="rounded-lg border bg-background p-3.5">
                                        <p className="text-xs font-medium text-muted-foreground">
                                            Savings
                                        </p>
                                        <p className="mt-1.5 text-lg font-semibold">
                                            LKR 253,350
                                        </p>
                                    </div>
                                </div>
                                <div className="space-y-3">
                                    <div className="flex items-center justify-between">
                                        <p className="text-sm font-medium">
                                            Recent activity
                                        </p>
                                        <BarChart3 className="size-4 text-muted-foreground" />
                                    </div>
                                    <div className="flex items-center gap-3 rounded-lg bg-muted/50 p-3">
                                        <span className="flex size-9 items-center justify-center rounded-md bg-primary/10 text-primary">
                                            <Landmark className="size-4" />
                                        </span>
                                        <div className="min-w-0 flex-1">
                                            <p className="text-sm font-medium">
                                                Salary deposit
                                            </p>
                                            <p className="text-xs text-muted-foreground">
                                                Today · Everyday account
                                            </p>
                                        </div>
                                        <span className="text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                                            +180,000
                                        </span>
                                    </div>
                                    <div className="flex items-center gap-3 rounded-lg bg-muted/50 p-3">
                                        <span className="flex size-9 items-center justify-center rounded-md bg-primary/10 text-primary">
                                            <CreditCard className="size-4" />
                                        </span>
                                        <div className="min-w-0 flex-1">
                                            <p className="text-sm font-medium">
                                                Weekly essentials
                                            </p>
                                            <p className="text-xs text-muted-foreground">
                                                Yesterday · Card
                                            </p>
                                        </div>
                                        <span className="text-sm font-semibold">
                                            −12,450
                                        </span>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </section>

                    <section className="border-y bg-muted/30">
                        <div className="mx-auto max-w-7xl px-5 py-20 sm:px-6 lg:px-8">
                            <div className="max-w-xl">
                                <p className="text-sm font-medium text-primary">
                                    A better daily money habit
                                </p>
                                <h2 className="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl">
                                    A ledger that makes sense at a glance.
                                </h2>
                            </div>
                            <div className="mt-10 grid gap-4 md:grid-cols-3">
                                {features.map((feature) => (
                                    <Card
                                        key={feature.title}
                                        className="gap-4 py-5 shadow-none"
                                    >
                                        <CardHeader className="px-5">
                                            <span className="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                                <feature.icon className="size-5" />
                                            </span>
                                            <CardTitle className="text-base">
                                                {feature.title}
                                            </CardTitle>
                                        </CardHeader>
                                        <CardContent className="px-5">
                                            <CardDescription className="leading-6">
                                                {feature.description}
                                            </CardDescription>
                                        </CardContent>
                                    </Card>
                                ))}
                            </div>
                        </div>
                    </section>

                    <section className="mx-auto max-w-7xl px-5 py-20 sm:px-6 lg:px-8">
                        <Card className="relative isolate overflow-hidden bg-foreground px-6 py-10 text-background shadow-lg shadow-foreground/20 sm:px-10 lg:py-12">
                            <div
                                aria-hidden="true"
                                className="pointer-events-none absolute -top-32 right-10 -z-10 size-72 rounded-full bg-emerald-400/15 blur-3xl"
                            />
                            <div className="grid w-full items-center gap-8 md:grid-cols-[auto_1fr_auto] md:gap-10">
                                <span className="flex size-14 items-center justify-center rounded-2xl bg-background/10 text-background">
                                    <WalletCards className="size-7" />
                                </span>
                                <div className="max-w-2xl space-y-3">
                                    <h2 className="text-3xl leading-tight font-semibold tracking-tight sm:text-4xl">
                                        Your money deserves a clearer picture.
                                    </h2>
                                    <p className="max-w-xl text-base leading-7 text-background/70">
                                        Create a deliberate record of where your
                                        money is and where it goes.
                                    </p>
                                </div>
                                <Button asChild variant="secondary" size="lg">
                                    <Link href={destination}>
                                        {destinationLabel}
                                        <ArrowRight />
                                    </Link>
                                </Button>
                            </div>
                        </Card>
                    </section>
                </main>

                <footer className="border-t">
                    <div className="mx-auto flex max-w-6xl items-center justify-between px-6 py-6 text-sm text-muted-foreground lg:px-8">
                        <span>
                            © {new Date().getFullYear()} {name}
                        </span>
                        <span>Personal finance, in balance.</span>
                    </div>
                </footer>
            </div>
        </>
    );
}
