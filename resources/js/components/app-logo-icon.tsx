import type { SVGAttributes } from 'react';

export default function AppLogoIcon(props: SVGAttributes<SVGElement>) {
    return (
        <svg {...props} viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M6 2.5A3.5 3.5 0 0 0 2.5 6v12A3.5 3.5 0 0 0 6 21.5h13a2.5 2.5 0 0 0 2.5-2.5V5A2.5 2.5 0 0 0 19 2.5H6Zm0 2h13a.5.5 0 0 1 .5.5v12.75a2.48 2.48 0 0 0-1.5-.5H6A1.5 1.5 0 0 1 4.5 16V6A1.5 1.5 0 0 1 6 4.5Zm0 14.25h12a.5.5 0 0 1 0 1H6a1.5 1.5 0 0 1-1.22-.63c.38-.24.8-.37 1.22-.37Z" />
            <path d="M7 7h8v1.75H7V7Zm0 3.5h8v1.75H7V10.5Zm0 3.5h5v1.75H7V14Z" />
        </svg>
    );
}
