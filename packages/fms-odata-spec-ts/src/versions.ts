/**
 * FileMaker Server version identifiers and feature flags.
 *
 * @see docs/12-version-deltas.md
 */

/** FileMaker Server version major numbers. */
export type FMVersionMajor = '20' | '21' | '22' | '26' | 'future';

/** Human-readable version names. */
export const FM_VERSION_NAMES: Record<FMVersionMajor, string> = {
  '20': 'Claris FileMaker 2023',
  '21': 'Claris FileMaker 2024',
  '22': 'Claris FileMaker 2025',
  '26': 'Claris FileMaker 2026',
  future: 'Future / next',
};

/** Version status. */
export type FMVersionStatus = 'supported' | 'current' | 'future';

/**
 * OData protocol version implemented by FileMaker.
 *
 * v20.x implements OData 4.0. v21.x onward implements partial OData 4.01
 * at intermediate conformance level with some exceptions.
 */
export const ODATA_PROTOCOL_VERSION = '4.01' as const;

/** OData conformance level. */
export const ODATA_CONFORMANCE_LEVEL = 'intermediate' as const;

/** Default server-driven page size (records per page). */
export const DEFAULT_PAGE_SIZE = 10000;

/** Feature flags for a specific FileMaker Server version. */
export interface FMFeatureFlags {
  serviceDocument: boolean;
  metadata: boolean;
  databaseListing: boolean;
  tableListing: boolean;
  recordCRUD: boolean;
  recordReferences: boolean;
  crossJoin: boolean;
  batch: boolean;
  scripts: boolean;
  scriptsByFMSID: boolean;
  scriptListing: boolean;
  containerBinaryUpload: boolean;
  containerBase64Upload: boolean;
  containerDownload: boolean;
  schemaModification: boolean;
  webhooks: boolean;
  webhookQueryHeaders: boolean;
  applyAggregation: boolean;
  typeCasting: boolean;
  parameterizedFilters: boolean;
  simplifiedQuerySyntax: boolean;
  nestedQueries: boolean;
  batchPreferenceInheritance: boolean;
  metadataFiltering: boolean;
  immutableIdUrls: boolean;
  fmComment: boolean;
  aiAnnotation: boolean;
  computedAnnotation: boolean;
  serverVersionAnnotation: boolean;
  enrichedFMComment: boolean;
  authBasic: boolean;
  authOAuth: boolean;
}

/** Query option availability for a specific version. */
export interface FMQueryOptionFlags {
  $filter: boolean;
  $select: boolean;
  $orderby: boolean;
  $top: boolean;
  $skip: boolean;
  $expand: boolean;
  $count: boolean;
  $apply: boolean;
  $search: boolean;
  $compute: boolean;
}

/** OData protocol version for a specific FileMaker Server version. */
export type ODataProtocolVersion = '4.0' | '4.01';

/** Complete version descriptor. */
export interface FMVersionInfo {
  major: FMVersionMajor;
  name: string;
  releaseYear: number | null;
  internalVersion: string;
  status: FMVersionStatus;
  odataProtocolVersion: ODataProtocolVersion;
  features: FMFeatureFlags;
  queryOptions: FMQueryOptionFlags;
}

/**
 * Feature flag matrix across all supported versions.
 * Import this to programmatically check feature availability.
 */
export const FM_VERSION_MATRIX: Record<FMVersionMajor, FMVersionInfo> = {
  '20': {
    major: '20',
    name: 'Claris FileMaker 2023',
    releaseYear: 2023,
    internalVersion: '20.x',
    status: 'supported',
    odataProtocolVersion: '4.0',
    features: {
      serviceDocument: true, metadata: true, databaseListing: true, tableListing: true,
      recordCRUD: true, recordReferences: true, crossJoin: true, batch: true,
      scripts: true, scriptsByFMSID: false, scriptListing: false,
      containerBinaryUpload: true, containerBase64Upload: true, containerDownload: true,
      schemaModification: true, webhooks: false, webhookQueryHeaders: false,
      applyAggregation: false, typeCasting: false, parameterizedFilters: false,
      simplifiedQuerySyntax: false, nestedQueries: false, batchPreferenceInheritance: false,
      metadataFiltering: false, immutableIdUrls: false,
      fmComment: false, aiAnnotation: false, computedAnnotation: false,
      serverVersionAnnotation: false, enrichedFMComment: false,
      authBasic: true, authOAuth: false,
    },
    queryOptions: {
      $filter: true, $select: true, $orderby: true, $top: true, $skip: true,
      $expand: true, $count: true, $apply: false, $search: false, $compute: false,
    },
  },
  '21': {
    major: '21',
    name: 'Claris FileMaker 2024',
    releaseYear: 2024,
    internalVersion: '21.x',
    status: 'supported',
    odataProtocolVersion: '4.01',
    features: {
      serviceDocument: true, metadata: true, databaseListing: true, tableListing: true,
      recordCRUD: true, recordReferences: true, crossJoin: true, batch: true,
      scripts: true, scriptsByFMSID: true, scriptListing: false,
      containerBinaryUpload: true, containerBase64Upload: true, containerDownload: true,
      schemaModification: true, webhooks: false, webhookQueryHeaders: false,
      applyAggregation: false, typeCasting: true, parameterizedFilters: true,
      simplifiedQuerySyntax: true, nestedQueries: true, batchPreferenceInheritance: true,
      metadataFiltering: false, immutableIdUrls: false,
      fmComment: true, aiAnnotation: true, computedAnnotation: false,
      serverVersionAnnotation: false, enrichedFMComment: false,
      authBasic: true, authOAuth: true,
    },
    queryOptions: {
      $filter: true, $select: true, $orderby: true, $top: true, $skip: true,
      $expand: true, $count: true, $apply: false, $search: false, $compute: false,
    },
  },
  '22': {
    major: '22',
    name: 'Claris FileMaker 2025',
    releaseYear: 2025,
    internalVersion: '22.x',
    status: 'supported',
    odataProtocolVersion: '4.01',
    features: {
      serviceDocument: true, metadata: true, databaseListing: true, tableListing: true,
      recordCRUD: true, recordReferences: true, crossJoin: true, batch: true,
      scripts: true, scriptsByFMSID: true, scriptListing: false,
      containerBinaryUpload: true, containerBase64Upload: true, containerDownload: true,
      schemaModification: true, webhooks: true, webhookQueryHeaders: true,
      applyAggregation: true, typeCasting: true, parameterizedFilters: true,
      simplifiedQuerySyntax: true, nestedQueries: true, batchPreferenceInheritance: true,
      metadataFiltering: true, immutableIdUrls: false,
      fmComment: true, aiAnnotation: true, computedAnnotation: false,
      serverVersionAnnotation: false, enrichedFMComment: false,
      authBasic: true, authOAuth: true,
    },
    queryOptions: {
      $filter: true, $select: true, $orderby: true, $top: true, $skip: true,
      $expand: true, $count: true, $apply: true, $search: false, $compute: false,
    },
  },
  '26': {
    major: '26',
    name: 'Claris FileMaker 2026',
    releaseYear: 2026,
    internalVersion: '26.x',
    status: 'current',
    odataProtocolVersion: '4.01',
    features: {
      serviceDocument: true, metadata: true, databaseListing: true, tableListing: true,
      recordCRUD: true, recordReferences: true, crossJoin: true, batch: true,
      scripts: true, scriptsByFMSID: true, scriptListing: true,
      containerBinaryUpload: true, containerBase64Upload: true, containerDownload: true,
      schemaModification: true, webhooks: true, webhookQueryHeaders: true,
      applyAggregation: true, typeCasting: true, parameterizedFilters: true,
      simplifiedQuerySyntax: true, nestedQueries: true, batchPreferenceInheritance: true,
      metadataFiltering: true, immutableIdUrls: true,
      fmComment: true, aiAnnotation: true, computedAnnotation: true,
      serverVersionAnnotation: true, enrichedFMComment: true,
      authBasic: true, authOAuth: true,
    },
    queryOptions: {
      $filter: true, $select: true, $orderby: true, $top: true, $skip: true,
      $expand: true, $count: true, $apply: true, $search: false, $compute: false,
    },
  },
  future: {
    major: 'future',
    name: 'Future / next',
    releaseYear: null,
    internalVersion: 'unknown',
    status: 'future',
    odataProtocolVersion: '4.01',
    features: {
      serviceDocument: true, metadata: true, databaseListing: true, tableListing: true,
      recordCRUD: true, recordReferences: true, crossJoin: true, batch: true,
      scripts: true, scriptsByFMSID: true, scriptListing: true,
      containerBinaryUpload: true, containerBase64Upload: true, containerDownload: true,
      schemaModification: true, webhooks: true, webhookQueryHeaders: true,
      applyAggregation: true, typeCasting: true, parameterizedFilters: true,
      simplifiedQuerySyntax: true, nestedQueries: true, batchPreferenceInheritance: true,
      metadataFiltering: true, immutableIdUrls: true,
      fmComment: true, aiAnnotation: true, computedAnnotation: true,
      serverVersionAnnotation: true, enrichedFMComment: true,
      authBasic: true, authOAuth: true,
    },
    queryOptions: {
      $filter: true, $select: true, $orderby: true, $top: true, $skip: true,
      $expand: true, $count: true, $apply: true, $search: false, $compute: false,
    },
  },
};

/** Check if a feature is available in a given version. */
export function hasFeature(version: FMVersionMajor, feature: keyof FMFeatureFlags): boolean {
  return FM_VERSION_MATRIX[version]?.features[feature] ?? false;
}

/** Check if a query option is available in a given version. */
export function hasQueryOption(version: FMVersionMajor, option: keyof FMQueryOptionFlags): boolean {
  return FM_VERSION_MATRIX[version]?.queryOptions[option] ?? false;
}

/** Get the minimum version that supports a given feature. */
export function minVersionForFeature(feature: keyof FMFeatureFlags): FMVersionMajor | null {
  const order: FMVersionMajor[] = ['20', '21', '22', '26'];
  for (const v of order) {
    if (FM_VERSION_MATRIX[v].features[feature]) return v;
  }
  return null;
}
