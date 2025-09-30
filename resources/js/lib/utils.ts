import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function getCsrfToken(): string | null {
    return document.head.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? null;
}
