export interface LegalDocumentSection {
  id: string;
  title: string;
  body: string;
}

export function buildText(...lines: string[]): string {
  return lines.join('\n');
}

export function buildLink(label: string, url: string): string {
  return `[${label}](${url})`;
}

function escapeHtml(text: string): string {
  return text
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#39;');
}

function splitTrailingPunctuation(value: string): { url: string; trailing: string } {
  let url = value;
  let trailing = '';

  while (/[).,;:]$/.test(url)) {
    trailing = url.slice(-1) + trailing;
    url = url.slice(0, -1);
  }

  return { url, trailing };
}

export function formatLegalDocumentBody(body: string): string {
  const markdownLinks: string[] = [];

  const escapedBody = escapeHtml(body).replace(
    /\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/g,
    (_, label: string, url: string) => {
      const token = `__DOC_LINK_${markdownLinks.length}__`;
      const safeLabel = escapeHtml(label);
      const safeUrl = escapeHtml(url);

      markdownLinks.push(`<a href="${safeUrl}" rel="noopener noreferrer">${safeLabel}</a>`);

      return token;
    },
  );

  const formattedBody = escapedBody
    .replace(/https?:\/\/[^\s<]+/g, (rawUrl) => {
      const { url, trailing } = splitTrailingPunctuation(rawUrl);
      const safeUrl = escapeHtml(url);

      return `<a href="${safeUrl}" rel="noopener noreferrer">${safeUrl}</a>${trailing}`;
    })
    .replace(/\n/g, '<br>');

  return markdownLinks.reduce((result, link, index) => result.replace(`__DOC_LINK_${index}__`, link), formattedBody);
}
